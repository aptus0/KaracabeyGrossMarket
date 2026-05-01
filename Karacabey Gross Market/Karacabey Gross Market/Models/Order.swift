import Foundation

struct Order: Codable, Identifiable {
    let id: Int
    let number: String
    let status: String
    let totalCents: Int
    let subtotalCents: Int?
    let shippingCents: Int?
    let discountCents: Int?
    let items: [OrderItem]?
    let address: Address?
    let createdAt: String
    let updatedAt: String?
    let payment: Payment?
    let merchantOid: String?
    let checkoutRef: String?
    let customerPhone: String?
    let customerEmail: String?

    enum CodingKeys: String, CodingKey {
        case id, number, status, items, address, payment
        case totalCents = "total_cents"
        case subtotalCents = "subtotal_cents"
        case shippingCents = "shipping_cents"
        case discountCents = "discount_cents"
        case createdAt = "created_at"
        case updatedAt = "updated_at"
        case merchantOid = "merchant_oid"
        case checkoutRef = "checkout_ref"
        case customerPhone = "customer_phone"
        case customerEmail = "customer_email"
    }

    var formattedTotal: String { String(format: "₺%.2f", Double(totalCents) / 100) }

    var statusLabel: String {
        switch status {
        case "pending":    return "Bekliyor"
        case "confirmed":  return "Onaylandı"
        case "preparing":  return "Hazırlanıyor"
        case "processing": return "İşleniyor"
        case "shipped":    return "Kargoda"
        case "delivered":  return "Teslim Edildi"
        case "cancelled":  return "İptal"
        case "refunded":   return "Para İade Edildi"
        default:           return status.capitalized
        }
    }

    var statusColor: String {
        switch status {
        case "delivered":  return "green"
        case "cancelled":  return "red"
        case "refunded":   return "red"
        case "shipped":    return "blue"
        case "processing": return "purple"
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
