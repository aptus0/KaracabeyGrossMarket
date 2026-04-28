import Foundation
import Combine

@MainActor
class CategoriesViewModel: ObservableObject {
    @Published var categories: [Category] = []
    @Published var isLoading = false
    @Published var errorMessage: String?
    
    func fetchCategories() async {
        isLoading = true
        errorMessage = nil
        do {
            let response: ArrayResponse<Category> = try await APIClient.shared.request(AppEndpoints.getCategories)
            self.categories = response.data
        } catch {
            self.errorMessage = "Kategoriler yüklenirken bir hata oluştu."
            print("Fetch categories error: \(error)")
        }
        isLoading = false
    }
}
