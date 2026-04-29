import Foundation

@MainActor
final class HomeViewModel: ObservableObject {
    @Published var featuredProducts: [Product] = []
    @Published var categories: [Category] = []
    @Published var isLoading = false
    @Published var errorMessage: String?

    func load() async {
        guard featuredProducts.isEmpty else { return }
        isLoading    = true
        errorMessage = nil
        defer { isLoading = false }
        async let p: PaginatedResponse<Product> = APIClient.shared.request(
            ProductsEndpoint.list(page: 1, perPage: 12, category: nil, query: nil)
        )
        async let c: ArrayResponse<Category> = APIClient.shared.request(CategoriesEndpoint.list)
        do {
            let (products, cats) = try await (p, c)
            featuredProducts = products.data
            categories       = cats.data
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? "Yüklenemedi."
        }
    }
}
