import Foundation
import Combine

@MainActor
final class SearchViewModel: ObservableObject {
    @Published var searchQuery = ""
    @Published var results: [Product] = []
    @Published var suggestions: [ProductSuggestion] = []
    @Published var isLoading = false
    @Published var errorMessage: String?

    private var searchTask: Task<Void, Never>?

    func search(query: String) {
        searchQuery = query
        searchTask?.cancel()

        guard !query.trimmingCharacters(in: .whitespaces).isEmpty else {
            results = []
            suggestions = []
            return
        }

        searchTask = Task {
            isLoading = true
            errorMessage = nil
            defer { isLoading = false }

            do {
                let response: PaginatedResponse<Product> = try await APIClient.shared.request(
                    ProductsEndpoint.list(page: 1, perPage: 20, category: nil, query: query)
                )
                self.results = response.data
            } catch {
                self.errorMessage = "Arama başarısız."
            }
        }
    }

    func getSuggestions(for query: String) {
        guard !query.isEmpty else {
            suggestions = []
            return
        }

        Task {
            do {
                let response: ArrayResponse<ProductSuggestion> = try await APIClient.shared.request(
                    ProductsEndpoint.suggest(query: query)
                )
                self.suggestions = response.data
            } catch {
                self.suggestions = []
            }
        }
    }
}
