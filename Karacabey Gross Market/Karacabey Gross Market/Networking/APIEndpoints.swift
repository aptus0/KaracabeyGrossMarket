import Foundation

// MARK: - Products
enum ProductsEndpoint: Endpoint {
    case list(page: Int, perPage: Int, category: String?, query: String?)
    case detail(slug: String)
    case suggest(query: String)

    var path: String {
        switch self {
        case .list:                return "/products"
        case .detail(let slug):   return "/products/\(slug)"
        case .suggest:            return "/products/suggest"
        }
    }

    var queryItems: [URLQueryItem]? {
        switch self {
        case let .list(page, perPage, category, query):
            var items: [URLQueryItem] = [
                URLQueryItem(name: "page",     value: String(page)),
                URLQueryItem(name: "per_page", value: String(perPage)),
            ]
            if let c = category { items.append(URLQueryItem(name: "category", value: c)) }
            if let q = query    { items.append(URLQueryItem(name: "q",        value: q)) }
            return items
        case .suggest(let query):
            return [URLQueryItem(name: "q", value: query)]
        case .detail:
            return nil
        }
    }
}

// MARK: - Categories
enum CategoriesEndpoint: Endpoint {
    case list
    case detail(slug: String)

    var path: String {
        switch self {
        case .list:              return "/categories"
        case .detail(let slug): return "/categories/\(slug)"
        }
    }
}

// MARK: - Auth
enum AuthEndpoint: Endpoint {
    case login(LoginRequest)
    case register(RegisterRequest)
    case me
    case logout

    var path: String {
        switch self {
        case .login:    return "/auth/login"
        case .register: return "/auth/register"
        case .me:       return "/auth/me"
        case .logout:   return "/auth/logout"
        }
    }

    var method: String {
        switch self {
        case .login, .register, .logout: return "POST"
        default:                         return "GET"
        }
    }

    var body: Data? {
        switch self {
        case .login(let r):    return try? JSONEncoder().encode(r)
        case .register(let r): return try? JSONEncoder().encode(r)
        default:               return nil
        }
    }
}

// MARK: - Cart
enum CartEndpoint: Endpoint {
    case show
    case addItem(AddToCartRequest)
    case updateItem(id: Int, UpdateCartItemRequest)
    case removeItem(id: Int)
    case clear
    case applyCoupon(code: String)
    case removeCoupon

    var path: String {
        switch self {
        case .show, .clear:              return "/cart"
        case .addItem:                   return "/cart/items"
        case .updateItem(let id, _):    return "/cart/items/\(id)"
        case .removeItem(let id):       return "/cart/items/\(id)"
        case .applyCoupon, .removeCoupon: return "/cart/coupon"
        }
    }

    var method: String {
        switch self {
        case .show:                    return "GET"
        case .addItem, .applyCoupon:   return "POST"
        case .updateItem:              return "PATCH"
        case .removeItem, .clear, .removeCoupon: return "DELETE"
        }
    }

    var body: Data? {
        switch self {
        case .addItem(let r):           return try? JSONEncoder().encode(r)
        case .updateItem(_, let r):     return try? JSONEncoder().encode(r)
        case .applyCoupon(let code):
            return try? JSONEncoder().encode(["code": code])
        default:                        return nil
        }
    }
}

// MARK: - Orders
enum OrdersEndpoint: Endpoint {
    case list
    case detail(id: Int)

    var path: String {
        switch self {
        case .list:             return "/orders"
        case .detail(let id):  return "/orders/\(id)"
        }
    }
}

// MARK: - Favorites
enum FavoritesEndpoint: Endpoint {
    case list
    case add(slug: String)
    case remove(slug: String)

    var path: String {
        switch self {
        case .list:             return "/favorites"
        case .add(let slug):    return "/favorites/\(slug)"
        case .remove(let slug): return "/favorites/\(slug)"
        }
    }

    var method: String {
        switch self {
        case .add:    return "POST"
        case .remove: return "DELETE"
        default:      return "GET"
        }
    }
}

// MARK: - Addresses
enum AddressEndpoint: Endpoint {
    case list
    case create(body: Data)
    case update(id: Int, body: Data)
    case delete(id: Int)

    var path: String {
        switch self {
        case .list, .create:     return "/addresses"
        case .update(let id, _): return "/addresses/\(id)"
        case .delete(let id):    return "/addresses/\(id)"
        }
    }

    var method: String {
        switch self {
        case .list:   return "GET"
        case .create: return "POST"
        case .update: return "PUT"
        case .delete: return "DELETE"
        }
    }

    var body: Data? {
        switch self {
        case .create(let b):    return b
        case .update(_, let b): return b
        default:                return nil
        }
    }
}
