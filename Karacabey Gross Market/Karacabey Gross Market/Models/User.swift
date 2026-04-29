import Foundation

struct User: Codable, Identifiable {
    let id: Int
    let name: String
    let email: String
    let phone: String?
    let createdAt: String?

    enum CodingKeys: String, CodingKey {
        case id, name, email, phone
        case createdAt = "created_at"
    }
}

struct Address: Codable, Identifiable {
    let id: Int
    let title: String
    let fullName: String
    let phone: String
    let city: String
    let district: String
    let addressLine: String
    let isDefault: Bool

    enum CodingKeys: String, CodingKey {
        case id, title, phone, city, district
        case fullName    = "full_name"
        case addressLine = "address_line"
        case isDefault   = "is_default"
    }

    var fullAddress: String { "\(addressLine), \(district) / \(city)" }
}

struct AuthResponse: Codable {
    let token: String
    let user: User
}

struct LoginRequest: Codable {
    let email: String
    let password: String
}

struct RegisterRequest: Codable {
    let name: String
    let email: String
    let password: String
    let passwordConfirmation: String

    enum CodingKeys: String, CodingKey {
        case name, email, password
        case passwordConfirmation = "password_confirmation"
    }
}
