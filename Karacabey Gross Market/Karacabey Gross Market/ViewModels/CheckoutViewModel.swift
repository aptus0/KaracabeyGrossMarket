import Foundation
import Combine

@MainActor
final class CheckoutViewModel: ObservableObject {
    @Published var isLoading = false
    @Published var errorMessage: String?
    @Published var checkoutResponse: CheckoutResponse?

    @Published var customerName = ""
    @Published var customerEmail = ""
    @Published var customerPhone = ""
    @Published var shippingCity = ""
    @Published var shippingDistrict = ""
    @Published var shippingAddress = ""

    private let cartManager = CartManager.shared

    func initiateCheckout() async -> Bool {
        guard validateForm() else { return false }

        isLoading = true
        errorMessage = nil
        defer { isLoading = false }

        do {
            let request = CheckoutRequest(
                cartItems: [],
                customerName: customerName,
                customerEmail: customerEmail,
                customerPhone: customerPhone,
                shippingCity: shippingCity,
                shippingDistrict: shippingDistrict,
                shippingAddress: shippingAddress
            )

            let response: CheckoutResponse = try await APIClient.shared.request(PaymentEndpoint.checkout(request))
            checkoutResponse = response
            return true
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? "Checkout başarısız."
            return false
        }
    }

    private func validateForm() -> Bool {
        if customerName.trimmingCharacters(in: .whitespaces).isEmpty {
            errorMessage = "Lütfen adınızı giriniz."
            return false
        }
        if customerEmail.trimmingCharacters(in: .whitespaces).isEmpty {
            errorMessage = "Lütfen e-posta adresinizi giriniz."
            return false
        }
        if customerPhone.trimmingCharacters(in: .whitespaces).isEmpty {
            errorMessage = "Lütfen telefon numaranızı giriniz."
            return false
        }
        if shippingCity.trimmingCharacters(in: .whitespaces).isEmpty {
            errorMessage = "Lütfen şehir seçiniz."
            return false
        }
        if shippingAddress.trimmingCharacters(in: .whitespaces).isEmpty {
            errorMessage = "Lütfen adres giriniz."
            return false
        }
        return true
    }
}
