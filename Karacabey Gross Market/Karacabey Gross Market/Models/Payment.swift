import Foundation

enum PaymentStatus: String, Codable {
    case pending
    case processing
    case completed
    case failed
    case refunded
}

struct Payment: Codable, Identifiable {
    let id: Int
    let orderId: Int
    let provider: String
    let merchantOid: String
    let status: PaymentStatus
    let amountCents: Int
    let capturedAmountCents: Int?
    let currency: String
    let paymentType: String?
    let failedReasonCode: String?
    let failedReasonMsg: String?
    let confirmedAt: String?

    enum CodingKeys: String, CodingKey {
        case id, orderId = "order_id", provider, status
        case merchantOid = "merchant_oid"
        case amountCents = "amount_cents"
        case capturedAmountCents = "captured_amount_cents"
        case currency
        case paymentType = "payment_type"
        case failedReasonCode = "failed_reason_code"
        case failedReasonMsg = "failed_reason_msg"
        case confirmedAt = "confirmed_at"
    }

    var displayAmount: String { String(format: "%.2f ₺", Double(amountCents) / 100) }
}

struct PaymentTokenResponse: Codable {
    let token: String
    let status: String
    let reason: String?
}

struct PaymentStatusResponse: Codable {
    let data: PaymentStatusData
}

struct PaymentStatusData: Codable {
    let local: Payment
    let gateway: GatewayStatus?
}

struct GatewayStatus: Codable {
    let status: String?
    let amount: String?
    let oid: String?
}