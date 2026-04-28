import Foundation

struct Category: Codable, Identifiable {
    let id: Int
    let parentId: Int?
    let name: String
    let slug: String
    let description: String?
    let imageUrl: String?
    let children: [Category]?
    let products: [Product]?
    
    enum CodingKeys: String, CodingKey {
        case id, name, slug, description, children, products
        case parentId = "parent_id"
        case imageUrl = "image_url"
    }
}
