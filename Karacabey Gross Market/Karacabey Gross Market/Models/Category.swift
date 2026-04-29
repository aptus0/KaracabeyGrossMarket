import Foundation

struct Category: Codable, Identifiable, Hashable {
    let id: Int
    let name: String
    let slug: String
    let description: String?
    let imageUrl: String?

    enum CodingKeys: String, CodingKey {
        case id, name, slug, description
        case imageUrl = "image_url"
    }
}
