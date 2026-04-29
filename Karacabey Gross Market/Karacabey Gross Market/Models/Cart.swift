import Foundation

struct Cart: Codable {
    let items: [CartItem]
    let subtotalCents: Int
    let discountCents: Int
    let totalCents: Int
    let coupon: AppliedCoupon?

    enum CodingKeys: String, CodingKey {
        case items, coupon
        case subtotalCents = "subtotal_cents"
        case discountCents = "discount_cents"
        case totalCents    = "total_cents"
    }

    var formattedTotal: String { String(format: "₺%.2f", Double(totalCents) / 100) }
    var formattedSubtotal: String { String(format: "₺%.2f", Double(subtotalCents) / 100) }
    var formattedDiscount: String { String(format: "₺%.2f", Double(discountCents) / 100) }
    var totalCount: Int { items.reduce(0) { $0 + $1.quantity } }
}

struct CartItem: Codable, Identifiable {
    let id: Int
    let product: Product
    let quantity: Int
    let unitPriceCents: Int
    let lineTotalCents: Int

    enum CodingKeys: String, CodingKey {
        case id, product, quantity
        case unitPriceCents = "unit_price_cents"
        case lineTotalCents = "line_total_cents"
    }

    var formattedLineTotal: String { String(format: "₺%.2f", Double(lineTotalCents) / 100) }
}

struct AppliedCoupon: Codable {
    let code: String
    let discountCents: Int

    enum CodingKeys: String, CodingKey {
        case code
        case discountCents = "discount_cents"
    }
}

struct AddToCartRequest: Codable {
    let productSlug: String
    let quantity: Int

    enum CodingKeys: String, CodingKey {
        case quantity
        case productSlug = "product_slug"
    }
}

struct UpdateCartItemRequest: Codable {
    let quantity: Int
}
