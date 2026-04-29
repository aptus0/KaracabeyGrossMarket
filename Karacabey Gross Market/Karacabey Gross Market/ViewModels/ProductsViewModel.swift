import Foundation

@MainActor
final class ProductsViewModel: ObservableObject {
    @Published var products: [Product] = []
    @Published var isLoading = false
    @Published var isLoadingMore = false
    @Published var errorMessage: String?
    @Published var searchQuery = ""
    @Published var selectedCategory: String?

    private var currentPage = 1
    private var lastPage    = 1
    var hasMore: Bool { currentPage < lastPage }

    // MARK: - Load / Refresh

    func load(category: String? = nil) async {
        selectedCategory = category
        currentPage      = 1
        products         = []
        await fetchPage()
    }

    func loadMore() async {
        guard hasMore, !isLoadingMore else { return }
        currentPage += 1
        await fetchPage(isMore: true)
    }

    func search(_ query: String) async {
        searchQuery = query
        currentPage = 1
        products    = []
        await fetchPage()
    }

    // MARK: - Private

    private func fetchPage(isMore: Bool = false) async {
        if isMore { isLoadingMore = true } else { isLoading = true }
        defer { isLoading = false; isLoadingMore = false }
        errorMessage = nil
        do {
            let q = searchQuery.trimmingCharacters(in: .whitespaces)
            let response: PaginatedResponse<Product> = try await APIClient.shared.request(
                ProductsEndpoint.list(
                    page:     currentPage,
                    perPage:  24,
                    category: selectedCategory,
                    query:    q.isEmpty ? nil : q
                )
            )
            lastPage = response.lastPage
            products.append(contentsOf: response.data)
        } catch {
            currentPage  = max(1, currentPage - 1)
            errorMessage = (error as? NetworkError)?.errorDescription ?? "Ürünler yüklenemedi."
        }
    }
}
