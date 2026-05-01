import Foundation

@MainActor
class PaymentService {
    static let shared = PaymentService()

    private let apiClient = APIClient.shared

    func process3DSecurePayment(
        orderId: Int,
        cardToken: String? = nil,
        cardId: String? = nil,
        amount: Int,
        currency: String = "TRY"
    ) async throws -> Payment3DSecureResponse {
        let request = Payment3DSecureRequest(
            orderId: orderId,
            cardToken: cardToken,
            cardId: cardId,
            amount: amount,
            currency: currency
        )

        // 1. Get 3D Secure HTML from backend
        // This will be a custom endpoint in PayTR integration
        let endpoint = PaymentEndpoint.checkout(CheckoutRequest(
            customer: CustomerInfo(name: "", email: "", phone: ""),
            shipping: ShippingInfo(city: nil, district: nil, address: ""),
            cartToken: nil
        ))

        // In real scenario, you'd call a specific 3D endpoint
        // For now, returning a mock response structure
        return Payment3DSecureResponse(
            status: "pending",
            htmlContent: nil,
            redirectUrl: nil,
            paymentId: orderId
        )
    }

    func validateCardToken(cardNumber: String, expiryMonth: Int, expiryYear: Int, cvv: String) async throws -> String {
        // This would call PayTR tokenization endpoint
        // For security, card details should NEVER be sent to your own server
        // Instead, use PayTR's frontend library to tokenize
        let request = CardTokenRequest(
            cardNumber: cardNumber,
            cardholderName: "User",
            expiryMonth: expiryMonth,
            expiryYear: expiryYear,
            cvv: cvv
        )

        // Mock token - in reality, this comes from PayTR.js library
        return "token_" + String(UUID().uuidString.prefix(16))
    }

    func fetchSavedCards() async throws -> [PaymentCard] {
        // GET /api/payment-methods
        let response: ArrayResponse<PaymentCard> = try await apiClient.request(
            PaymentCardsEndpoint.list
        )
        return response.data
    }

    func deleteSavedCard(cardId: String) async throws {
        try await apiClient.requestEmpty(
            PaymentCardsEndpoint.delete(id: cardId)
        )
    }

    func setDefaultCard(cardId: String) async throws {
        try await apiClient.requestEmpty(
            PaymentCardsEndpoint.setDefault(id: cardId)
        )
    }
}

// MARK: - API Endpoints for Payment
enum PaymentCardsEndpoint: Endpoint {
    case list
    case delete(id: String)
    case setDefault(id: String)

    var path: String {
        switch self {
        case .list:
            return "/payment-methods"
        case .delete(let id):
            return "/payment-methods/\(id)"
        case .setDefault(let id):
            return "/payment-methods/\(id)/default"
        }
    }

    var method: String {
        switch self {
        case .list:
            return "GET"
        case .delete:
            return "DELETE"
        case .setDefault:
            return "POST"
        }
    }
}
