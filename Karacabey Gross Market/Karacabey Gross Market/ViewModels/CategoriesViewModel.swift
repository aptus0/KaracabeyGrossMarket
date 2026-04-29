import Foundation

@MainActor
final class CategoriesViewModel: ObservableObject {
    @Published var categories: [Category] = []
    @Published var isLoading = false
    @Published var errorMessage: String?

    func load() async {
        guard categories.isEmpty else { return }
        isLoading    = true
        errorMessage = nil
        defer { isLoading = false }
        do {
            let response: ArrayResponse<Category> = try await APIClient.shared.request(CategoriesEndpoint.list)
            categories = response.data
        } catch {
            errorMessage = (error as? NetworkError)?.errorDescription ?? "Kategoriler yüklenemedi."
        }
    }
}
