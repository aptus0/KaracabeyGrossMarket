import Foundation
import Combine

@MainActor
class HomeViewModel: ObservableObject {
    @Published var products: [Product] = []
    @Published var categories: [Category] = []
    @Published var isLoadingProducts = false
    @Published var isLoadingCategories = false
    @Published var errorMessage: String?
    
    func fetchProducts() async {
        isLoadingProducts = true
        errorMessage = nil
        do {
            let response: PaginatedResponse<Product> = try await APIClient.shared.request(AppEndpoints.getProducts(page: 1))
            self.products = response.data
        } catch {
            self.errorMessage = "Ürünler yüklenirken bir hata oluştu."
            print("Fetch products error: \(error)")
        }
        isLoadingProducts = false
    }
    
    func fetchCategories() async {
        isLoadingCategories = true
        do {
            let response: ArrayResponse<Category> = try await APIClient.shared.request(AppEndpoints.getCategories)
            self.categories = response.data
        } catch {
            print("Fetch categories error: \(error)")
        }
        isLoadingCategories = false
    }
}
