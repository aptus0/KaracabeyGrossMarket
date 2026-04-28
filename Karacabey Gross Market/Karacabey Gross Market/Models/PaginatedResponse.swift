import Foundation

struct PaginatedResponse<T: Codable>: Codable {
    let data: [T]
    let total: Int?
    let perPage: Int?
    let currentPage: Int?
    let lastPage: Int?
    
    enum CodingKeys: String, CodingKey {
        case data
        case total
        case perPage = "per_page"
        case currentPage = "current_page"
        case lastPage = "last_page"
    }
}

struct ArrayResponse<T: Codable>: Codable {
    let data: [T]
}
