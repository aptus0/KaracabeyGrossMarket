import Foundation
import Combine

@MainActor
final class OrderDetailViewModel: ObservableObject {
    @Published var order: Order?
    @Published var payment: Payment?
    @Published var isLoading = false
    @Published var errorMessage: String?
    @Published var refreshing = false

    func load(orderId: Int) async {
        isLoading = true
        defer { isLoading = false }

        do {
            let response: SingleResponse<Order> = try await APIClient.shared.request(OrdersEndpoint.detail(id: orderId))
            self.order = response.data
            self.payment = response.data.payment
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? "Sipariş yüklenemedi."
        }
    }

    func refreshPaymentStatus() async {
        guard let paymentId = payment?.id else { return }
        refreshing = true
        defer { refreshing = false }

        do {
            let response: PaymentStatusResponse = try await APIClient.shared.request(PaymentEndpoint.status(paymentId: paymentId))
            self.payment = response.data.local
        } catch {
            errorMessage = "Ödeme durumu güncellenemedi."
        }
    }
}
