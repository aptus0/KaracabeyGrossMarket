import Foundation
import Combine

@MainActor
final class OrdersViewModel: ObservableObject {
    @Published var orders: [Order] = []
    @Published var selectedOrder: Order?
    @Published var isLoading = false
    @Published var errorMessage: String?

    func load() async {
        isLoading    = true
        errorMessage = nil
        defer { isLoading = false }
        do {
            let response: ArrayResponse<Order> = try await APIClient.shared.request(OrdersEndpoint.list)
            orders = response.data
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? "Siparişler yüklenemedi."
        }
    }

    func loadDetail(id: Int) async {
        do {
            let response: SingleResponse<Order> = try await APIClient.shared.request(OrdersEndpoint.detail(id: id))
            selectedOrder = response.data
        } catch { }
    }
}
