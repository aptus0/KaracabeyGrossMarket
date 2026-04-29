import Foundation
import Combine

@MainActor
final class CartManager: ObservableObject {
    static let shared = CartManager()
    private init() {}

    @Published var cart: Cart?
    @Published var isLoading = false
    @Published var errorMessage: String?

    var totalCount: Int { cart?.totalCount ?? 0 }
    var isEmpty: Bool   { cart?.items.isEmpty ?? true }

    // MARK: - Fetch

    func fetchCart() async {
        guard AuthManager.shared.isLoggedIn else { return }
        isLoading = true
        defer { isLoading = false }
        do {
            let response: SingleResponse<Cart> = try await APIClient.shared.request(CartEndpoint.show)
            cart = response.data
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? error.localizedDescription
        }
    }

    // MARK: - Add

    func addItem(slug: String, quantity: Int = 1) async {
        let req = AddToCartRequest(productSlug: slug, quantity: quantity)
        do {
            let response: SingleResponse<Cart> = try await APIClient.shared.request(CartEndpoint.addItem(req))
            cart = response.data
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? error.localizedDescription
        }
    }

    // MARK: - Update

    func updateItem(id: Int, quantity: Int) async {
        if quantity <= 0 { await removeItem(id: id); return }
        let req = UpdateCartItemRequest(quantity: quantity)
        do {
            let response: SingleResponse<Cart> = try await APIClient.shared.request(CartEndpoint.updateItem(id: id, req))
            cart = response.data
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? error.localizedDescription
        }
    }

    // MARK: - Remove

    func removeItem(id: Int) async {
        do {
            let response: SingleResponse<Cart> = try await APIClient.shared.request(CartEndpoint.removeItem(id: id))
            cart = response.data
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? error.localizedDescription
        }
    }

    // MARK: - Clear

    func clear() async {
        do {
            let response: SingleResponse<Cart> = try await APIClient.shared.request(CartEndpoint.clear)
            cart = response.data
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? error.localizedDescription
        }
    }

    // MARK: - Coupon

    func applyCoupon(_ code: String) async throws {
        let response: SingleResponse<Cart> = try await APIClient.shared.request(CartEndpoint.applyCoupon(code: code))
        cart = response.data
    }

    func removeCoupon() async {
        do {
            let response: SingleResponse<Cart> = try await APIClient.shared.request(CartEndpoint.removeCoupon)
            cart = response.data
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? error.localizedDescription
        }
    }

    // MARK: - Helpers

    func quantity(for slug: String) -> Int {
        cart?.items.first(where: { $0.product.slug == slug })?.quantity ?? 0
    }

    func isInCart(_ slug: String) -> Bool { quantity(for: slug) > 0 }

    func reset() { cart = nil }
}
