import Foundation

enum NetworkError: LocalizedError {
    case invalidURL
    case decodingError(Error)
    case serverError(Int, String?)
    case unauthorized
    case unknown(Error)

    var errorDescription: String? {
        switch self {
        case .invalidURL:              return "Geçersiz URL."
        case .decodingError:           return "Yanıt ayrıştırılamadı."
        case .serverError(let c, let m): return m ?? "Sunucu hatası (\(c))."
        case .unauthorized:            return "Oturum süresi doldu."
        case .unknown(let e):          return e.localizedDescription
        }
    }
}

// MARK: - AuthManager

@MainActor
final class AuthManager: ObservableObject {
    static let shared = AuthManager()

    @Published var token: String? {
        didSet {
            if let t = token { UserDefaults.standard.set(t, forKey: "kgm_token") }
            else              { UserDefaults.standard.removeObject(forKey: "kgm_token") }
        }
    }
    @Published var currentUser: User?

    private init() {
        self.token = UserDefaults.standard.string(forKey: "kgm_token")
    }

    var isLoggedIn: Bool { token != nil }

    func logout() {
        token       = nil
        currentUser = nil
    }
}

// MARK: - APIClient

final class APIClient {
    static let shared = APIClient()
    private init() {}

    // Geliştirme için Herd local URL; Release için APP_BASE_URL env / xcconfig kullanılabilir
    var baseURL: String {
        ProcessInfo.processInfo.environment["APP_BASE_URL"] ?? "http://karacabey-gross-market.test/api/v1"
    }

    func request<T: Decodable>(_ endpoint: any Endpoint) async throws -> T {
        var components = URLComponents(string: baseURL + endpoint.path)!
        if let qi = endpoint.queryItems, !qi.isEmpty {
            components.queryItems = qi
        }
        guard let url = components.url else { throw NetworkError.invalidURL }

        var req = URLRequest(url: url, timeoutInterval: 30)
        req.httpMethod = endpoint.method
        req.httpBody   = endpoint.body
        req.setValue("application/json", forHTTPHeaderField: "Content-Type")
        req.setValue("application/json", forHTTPHeaderField: "Accept")

        if let token = await AuthManager.shared.token {
            req.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
        }

        let (data, response): (Data, URLResponse)
        do {
            (data, response) = try await URLSession.shared.data(for: req)
        } catch {
            throw NetworkError.unknown(error)
        }

        guard let http = response as? HTTPURLResponse else { throw NetworkError.unknown(URLError(.badServerResponse)) }

        switch http.statusCode {
        case 200...299:
            do {
                return try JSONDecoder().decode(T.self, from: data)
            } catch {
                throw NetworkError.decodingError(error)
            }
        case 401:
            await AuthManager.shared.logout()
            throw NetworkError.unauthorized
        default:
            let msg = (try? JSONDecoder().decode(MessageResponse.self, from: data))?.message
            throw NetworkError.serverError(http.statusCode, msg)
        }
    }

    // Yanıt gerektirmeyen DELETE/POST için
    func requestEmpty(_ endpoint: any Endpoint) async throws {
        let _: MessageResponse = try await request(endpoint)
    }
}

// MARK: - Endpoint Protocol

protocol Endpoint {
    var path: String { get }
    var method: String { get }
    var body: Data? { get }
    var queryItems: [URLQueryItem]? { get }
}

extension Endpoint {
    var method: String     { "GET" }
    var body: Data?        { nil }
    var queryItems: [URLQueryItem]? { nil }
}
