import Foundation

struct Product: Codable, Identifiable {
    let id: Int
    let name: String
    let slug: String
    let brand: String?
    let priceCents: Int?
    let price: String?
    let compareAtPriceCents: Int?
    let imageUrl: String?
    
    enum CodingKeys: String, CodingKey {
        case id, name, slug, brand, price
        case priceCents = "price_cents"
        case compareAtPriceCents = "compare_at_price_cents"
        case imageUrl = "image_url"
    }
    
    var displayPrice: String {
        return price ?? "\(Double(priceCents ?? 0) / 100) ₺"
    }
}
