import Foundation
import Combine

@MainActor
final class AuthViewModel: ObservableObject {
    @Published var isLoading = false
    @Published var errorMessage: String?

    private let authManager = AuthManager.shared
    private let cartManager = CartManager.shared
    private let favManager  = FavoritesManager.shared

    func login(email: String, password: String) async -> Bool {
        isLoading    = true
        errorMessage = nil
        defer { isLoading = false }
        do {
            let req = LoginRequest(email: email.lowercased().trimmingCharacters(in: .whitespaces), password: password)
            let response: AuthResponse = try await APIClient.shared.request(AuthEndpoint.login(req))
            authManager.token       = response.token
            authManager.currentUser = response.user
            await cartManager.fetchCart()
            await favManager.fetchFavorites()
            return true
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? "Giriş başarısız."
            return false
        }
    }

    func register(name: String, email: String, password: String) async -> Bool {
        isLoading    = true
        errorMessage = nil
        defer { isLoading = false }
        do {
            let req = RegisterRequest(
                name:                 name.trimmingCharacters(in: .whitespaces),
                email:                email.lowercased().trimmingCharacters(in: .whitespaces),
                password:             password,
                passwordConfirmation: password
            )
            let response: AuthResponse = try await APIClient.shared.request(AuthEndpoint.register(req))
            authManager.token       = response.token
            authManager.currentUser = response.user
            return true
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? "Kayıt başarısız."
            return false
        }
    }

    func logout() async {
        isLoading = true
        defer { isLoading = false }
        if authManager.isLoggedIn {
            _ = try? await APIClient.shared.request(AuthEndpoint.logout) as MessageResponse
        }
        authManager.logout()
        cartManager.reset()
        favManager.reset()
    }

    func loadMe() async {
        guard authManager.isLoggedIn else { return }
        do {
            let response: SingleResponse<User> = try await APIClient.shared.request(AuthEndpoint.me)
            authManager.currentUser = response.data
        } catch { }
    }
}
