import Foundation

struct DeviceTokenRequest: Codable {
    let token: String
    let device_type: String
    let device_name: String?
}
