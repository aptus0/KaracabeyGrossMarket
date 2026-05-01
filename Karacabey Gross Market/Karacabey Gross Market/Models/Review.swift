import Foundation

struct Review: Codable, Identifiable {
    let id: Int
    let productId: Int
    let userId: Int?
    let rating: Int
    let title: String
    let comment: String?
    let userName: String
    let verified: Bool
    let helpfulCount: Int
    let createdAt: String?

    enum CodingKeys: String, CodingKey {
        case id, productId = "product_id", userId = "user_id"
        case rating, title, comment
        case userName = "user_name"
        case verified
        case helpfulCount = "helpful_count"
        case createdAt = "created_at"
    }

    var displayRating: String { String(rating) }
}

struct CreateReviewRequest: Codable {
    let productSlug: String
    let rating: Int
    let title: String
    let comment: String?

    enum CodingKeys: String, CodingKey {
        case productSlug = "product_slug"
        case rating, title, comment
    }
}
