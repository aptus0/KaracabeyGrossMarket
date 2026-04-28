import Foundation

enum AppEndpoints {
    case getProducts(page: Int)
    case getCategories
}

extension AppEndpoints: Endpoint {
    var path: String {
        switch self {
        case .getProducts:
            return "/products"
        case .getCategories:
            return "/categories"
        }
    }
    
    var queryItems: [URLQueryItem]? {
        switch self {
        case .getProducts(let page):
            return [URLQueryItem(name: "page", value: String(page))]
        case .getCategories:
            return nil
        }
    }
}
