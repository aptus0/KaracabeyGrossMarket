import Foundation
import Combine

@MainActor
final class ProductDetailViewModel: ObservableObject {
    @Published var product: Product?
    @Published var isLoading = false
    @Published var errorMessage: String?

    func load(slug: String) async {
        isLoading    = true
        errorMessage = nil
        defer { isLoading = false }
        do {
            let response: SingleResponse<Product> = try await APIClient.shared.request(ProductsEndpoint.detail(slug: slug))
            product = response.data
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? "Ürün yüklenemedi."
        }
    }
}
