import Foundation
import Combine

@MainActor
final class HomeViewModel: ObservableObject {
    @Published var featuredProducts: [Product] = []
    @Published var categories: [Category] = []
    @Published var stories: [Story] = []
    @Published var campaigns: [Campaign] = []
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
        
        // Use a generic response type for the data object returned by the API
        struct ContentData: Codable {
            let data: HomepageContent
        }
        
        async let h: ContentData = APIClient.shared.request(ContentEndpoint.homepage)
        
        do {
            let (products, cats, homepageData) = try await (p, c, h)
            featuredProducts = products.data
            categories       = cats.data
            stories          = homepageData.data.stories ?? []
            campaigns        = homepageData.data.campaigns ?? []
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? "Yüklenemedi."
        }
    }
}
