import Foundation

struct Order: Codable, Identifiable {
    let id: Int
    let number: String
    let status: String
    let totalCents: Int
    let items: [OrderItem]?
    let address: Address?
    let createdAt: String

    enum CodingKeys: String, CodingKey {
        case id, number, status, items, address
        case totalCents = "total_cents"
        case createdAt  = "created_at"
    }

    var formattedTotal: String { String(format: "₺%.2f", Double(totalCents) / 100) }

    var statusLabel: String {
        switch status {
        case "pending":    return "Bekliyor"
        case "confirmed":  return "Onaylandı"
        case "preparing":  return "Hazırlanıyor"
        case "shipped":    return "Kargoda"
        case "delivered":  return "Teslim Edildi"
        case "cancelled":  return "İptal"
        default:           return status.capitalized
        }
    }

    var statusColor: String {
        switch status {
        case "delivered":  return "green"
        case "cancelled":  return "red"
        case "shipped":    return "blue"
        default:           return "orange"
        }
    }
}

struct OrderItem: Codable, Identifiable {
    let id: Int
    let productName: String
    let quantity: Int
    let unitPriceCents: Int
    let imageUrl: String?

    enum CodingKeys: String, CodingKey {
        case id, quantity
        case productName  = "product_name"
        case unitPriceCents = "unit_price_cents"
        case imageUrl     = "image_url"
    }

    var formattedPrice: String { String(format: "₺%.2f", Double(unitPriceCents) / 100) }
}
