import Foundation

struct Product: Codable, Identifiable, Hashable {
    let id: Int
    let name: String
    let slug: String
    let description: String?
    let brand: String?
    let barcode: String?
    let priceCents: Int
    let price: String?
    let compareAtPriceCents: Int?
    let stockQuantity: Int
    let imageUrl: String?
    let categories: [ProductCategory]?

    enum CodingKeys: String, CodingKey {
        case id, name, slug, description, brand, barcode, price
        case priceCents          = "price_cents"
        case compareAtPriceCents = "compare_at_price_cents"
        case stockQuantity       = "stock_quantity"
        case imageUrl            = "image_url"
        case categories
    }

    var displayPrice: String { price ?? String(format: "%.2f ₺", Double(priceCents) / 100) }

    var hasDiscount: Bool {
        guard let compare = compareAtPriceCents else { return false }
        return compare > priceCents
    }

    var discountPercent: Int? {
        guard let compare = compareAtPriceCents, compare > priceCents, compare > 0 else { return nil }
        return Int(round(Double(compare - priceCents) / Double(compare) * 100))
    }

    var isInStock: Bool { stockQuantity > 0 }

    var originalPrice: String? {
        guard let compare = compareAtPriceCents, compare > priceCents else { return nil }
        return String(format: "%.2f ₺", Double(compare) / 100)
    }
}

struct ProductCategory: Codable, Identifiable, Hashable {
    let id: Int
    let name: String
    let slug: String
}

struct ProductSuggestion: Codable, Identifiable {
    let id: Int
    let name: String
    let slug: String
    let brand: String?
    let price: String?
    let imageUrl: String?

    enum CodingKeys: String, CodingKey {
        case id, name, slug, brand, price
        case imageUrl = "image_url"
    }
}
