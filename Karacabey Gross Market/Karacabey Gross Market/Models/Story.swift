import Foundation

struct Story: Codable, Identifiable {
    let id: Int
    let title: String
    let subtitle: String?
    let imageUrl: String?
    let categorySlug: String?
    let customUrl: String?
    let gradientStart: String?
    let gradientEnd: String?
    let icon: String?

    enum CodingKeys: String, CodingKey {
        case id
        case title
        case subtitle
        case imageUrl = "image_url"
        case categorySlug = "category_slug"
        case customUrl = "custom_url"
        case gradientStart = "gradient_start"
        case gradientEnd = "gradient_end"
        case icon
    }
}

struct HomepageContent: Codable {
    let stories: [Story]?
    let campaigns: [Campaign]?
    // blocks can be ignored for now unless needed
}

struct Campaign: Codable, Identifiable {
    let id: Int
    let name: String
    let slug: String
    let description: String?
    let bannerImageUrl: String?
    let colorHex: String?
    let discountType: String?
    let discountValue: Int?

    enum CodingKeys: String, CodingKey {
        case id
        case name
        case slug
        case description
        case bannerImageUrl = "banner_image_url"
        case colorHex = "color_hex"
        case discountType = "discount_type"
        case discountValue = "discount_value"
    }
}
