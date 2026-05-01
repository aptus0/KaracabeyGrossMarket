import Foundation

struct ProductFilter {
    var sortBy: SortOption = .newest
    var priceMin: Int?
    var priceMax: Int?
    var categories: [String] = []
    var inStockOnly: Bool = false
    var discountOnly: Bool = false
    var brands: [String] = []
    var ratings: Int? // 3, 4, 5

    enum SortOption: String, CaseIterable {
        case newest = "newest"
        case popular = "popular"
        case priceAsc = "price_asc"
        case priceDesc = "price_desc"
        case discount = "discount"
        case rating = "rating"

        var displayName: String {
            switch self {
            case .newest: return "Yeniler"
            case .popular: return "Popüler"
            case .priceAsc: return "Ucuzdan Pahalıya"
            case .priceDesc: return "Pahalıdan Ucuza"
            case .discount: return "En Çok İndirimli"
            case .rating: return "En Yüksek Puan"
            }
        }
    }

    var queryItems: [URLQueryItem] {
        var items: [URLQueryItem] = [
            URLQueryItem(name: "sort", value: sortBy.rawValue)
        ]

        if let min = priceMin {
            items.append(URLQueryItem(name: "price_min", value: String(min)))
        }
        if let max = priceMax {
            items.append(URLQueryItem(name: "price_max", value: String(max)))
        }
        if !categories.isEmpty {
            items.append(URLQueryItem(name: "categories", value: categories.joined(separator: ",")))
        }
        if inStockOnly {
            items.append(URLQueryItem(name: "in_stock", value: "1"))
        }
        if discountOnly {
            items.append(URLQueryItem(name: "has_discount", value: "1"))
        }
        if !brands.isEmpty {
            items.append(URLQueryItem(name: "brands", value: brands.joined(separator: ",")))
        }
        if let rating = ratings {
            items.append(URLQueryItem(name: "min_rating", value: String(rating)))
        }

        return items
    }
}
