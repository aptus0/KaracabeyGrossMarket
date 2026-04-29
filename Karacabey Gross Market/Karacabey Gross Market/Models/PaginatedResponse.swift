import Foundation

struct PaginatedResponse<T: Codable>: Codable {
    let data: [T]
    let total: Int
    let perPage: Int
    let currentPage: Int
    let lastPage: Int
    let from: Int?
    let to: Int?

    enum CodingKeys: String, CodingKey {
        case data, total, from, to
        case perPage     = "per_page"
        case currentPage = "current_page"
        case lastPage    = "last_page"
    }
}

struct SingleResponse<T: Codable>: Codable {
    let data: T
}

struct ArrayResponse<T: Codable>: Codable {
    let data: [T]
}

struct MessageResponse: Codable {
    let message: String
}
