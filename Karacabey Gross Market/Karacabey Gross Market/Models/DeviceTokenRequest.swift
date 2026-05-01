import Foundation

struct DeviceTokenRequest: Codable {
    let token: String
    let device_type: String
    let device_name: String?
}

struct DeviceTokenResponse: Codable {
    let data: DeviceTokenData

    struct DeviceTokenData: Codable {
        let id: Int
        let status: String
    }
}

struct DeviceTokenEndpoint: Endpoint {
    let token: String
    let deviceName: String

    var path: String { "/notifications/device-tokens" }
    var method: String { "POST" }

    var body: Data? {
        let request = DeviceTokenRequest(
            token: token,
            device_type: "ios",
            device_name: deviceName
        )
        return try? JSONEncoder().encode(request)
    }

    var queryItems: [URLQueryItem]? { nil }
}
