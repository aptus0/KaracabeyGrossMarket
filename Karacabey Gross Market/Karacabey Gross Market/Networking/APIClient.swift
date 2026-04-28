import Foundation
import Combine

enum NetworkError: Error {
    case invalidURL
    case noData
    case decodingError
    case serverError(Int)
    case unauthorized
    case unknown
}

protocol Endpoint {
    var path: String { get }
    var method: String { get }
    var body: Data? { get }
    var queryItems: [URLQueryItem]? { get }
}

extension Endpoint {
    var method: String { return "GET" }
    var body: Data? { return nil }
    var queryItems: [URLQueryItem]? { return nil }
}

class AuthManager: ObservableObject {
    static let shared = AuthManager()
    
    @Published var token: String? {
        didSet {
            if let token = token {
                UserDefaults.standard.set(token, forKey: "kgm_token")
            } else {
                UserDefaults.standard.removeObject(forKey: "kgm_token")
            }
        }
    }
    
    init() {
        self.token = UserDefaults.standard.string(forKey: "kgm_token")
    }
    
    var isLoggedIn: Bool {
        return token != nil
    }
}

class APIClient {
    static let shared = APIClient()
    
    // Herd URL for local development (or use IP like http://127.0.0.1:8000/api/v1)
    let baseURL = "http://karacabey-gross-market.test/api/v1"
    
    func request<T: Decodable>(_ endpoint: Endpoint) async throws -> T {
        var urlComponents = URLComponents(string: baseURL + endpoint.path)!
        urlComponents.queryItems = endpoint.queryItems
        
        guard let url = urlComponents.url else {
            throw NetworkError.invalidURL
        }
        
        var request = URLRequest(url: url)
        request.httpMethod = endpoint.method
        request.httpBody = endpoint.body
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue("application/json", forHTTPHeaderField: "Accept")
        
        if let token = AuthManager.shared.token {
            request.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
        }
        
        let (data, response) = try await URLSession.shared.data(for: request)
        
        guard let httpResponse = response as? HTTPURLResponse else {
            throw NetworkError.unknown
        }
        
        switch httpResponse.statusCode {
        case 200...299:
            do {
                let decodedResponse = try JSONDecoder().decode(T.self, from: data)
                return decodedResponse
            } catch {
                print("Decoding error: \(error)")
                throw NetworkError.decodingError
            }
        case 401:
            // Handle unauthorized globally if needed
            DispatchQueue.main.async {
                AuthManager.shared.token = nil
            }
            throw NetworkError.unauthorized
        default:
            print("Server error: \(httpResponse.statusCode)")
            throw NetworkError.serverError(httpResponse.statusCode)
        }
    }
}
