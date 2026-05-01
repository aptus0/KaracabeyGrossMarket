import Foundation

struct PaymentCard: Codable, Identifiable {
    let id: String
    let cardholderName: String
    let cardNumber: String  // Masked: 4111****1111
    let expiryMonth: Int
    let expiryYear: Int
    let brand: CardBrand
    let isDefault: Bool
    let createdAt: String?

    enum CodingKeys: String, CodingKey {
        case id
        case cardholderName = "cardholder_name"
        case cardNumber = "card_number"
        case expiryMonth = "expiry_month"
        case expiryYear = "expiry_year"
        case brand, isDefault = "is_default"
        case createdAt = "created_at"
    }

    enum CardBrand: String, Codable {
        case visa = "VISA"
        case mastercard = "MASTERCARD"
        case amex = "AMEX"
        case troy = "TROY"

        var displayName: String {
            switch self {
            case .visa: return "Visa"
            case .mastercard: return "Mastercard"
            case .amex: return "American Express"
            case .troy: return "Troy"
            }
        }

        var icon: String {
            switch self {
            case .visa: return "creditcard.circle.fill"
            case .mastercard: return "creditcard"
            case .amex: return "creditcard.circle"
            case .troy: return "creditcard.fill"
            }
        }
    }
}

struct CardInputModel {
    var cardNumber: String = ""
    var cardholderName: String = ""
    var expiryMonth: String = ""
    var expiryYear: String = ""
    var cvv: String = ""

    var isValid: Bool {
        let cardNumberValid = cardNumber.replacingOccurrences(of: " ", with: "").count == 16
        let nameValid = !cardholderName.trimmingCharacters(in: .whitespaces).isEmpty
        let expiryMonthValid = Int(expiryMonth) ?? 0 > 0 && Int(expiryMonth) ?? 0 <= 12
        let expiryYearValid = Int(expiryYear) ?? 0 > Date().year
        let cvvValid = cvv.count >= 3 && cvv.count <= 4

        return cardNumberValid && nameValid && expiryMonthValid && expiryYearValid && cvvValid
    }

    var displayCardNumber: String {
        let cleaned = cardNumber.replacingOccurrences(of: " ", with: "")
        guard cleaned.count >= 4 else { return cardNumber }
        let lastFour = String(cleaned.suffix(4))
        return "****\(lastFour)"
    }
}

struct CardTokenRequest: Codable {
    let cardNumber: String
    let cardholderName: String
    let expiryMonth: Int
    let expiryYear: Int
    let cvv: String

    enum CodingKeys: String, CodingKey {
        case cardNumber = "card_number"
        case cardholderName = "cardholder_name"
        case expiryMonth = "expiry_month"
        case expiryYear = "expiry_year"
        case cvv
    }
}

struct Payment3DSecureRequest: Codable {
    let orderId: Int
    let cardToken: String?
    let cardId: String?  // For saved cards
    let amount: Int
    let currency: String

    enum CodingKeys: String, CodingKey {
        case orderId = "order_id"
        case cardToken = "card_token"
        case cardId = "card_id"
        case amount, currency
    }
}

struct Payment3DSecureResponse: Codable {
    let status: String
    let htmlContent: String?  // 3D Secure form
    let redirectUrl: String?
    let paymentId: Int

    enum CodingKeys: String, CodingKey {
        case status
        case htmlContent = "html_content"
        case redirectUrl = "redirect_url"
        case paymentId = "payment_id"
    }
}

extension Date {
    var year: Int {
        Calendar.current.component(.year, from: self)
    }
}
