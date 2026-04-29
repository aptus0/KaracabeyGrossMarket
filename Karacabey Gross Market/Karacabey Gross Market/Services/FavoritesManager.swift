import Foundation

@MainActor
final class FavoritesManager: ObservableObject {
    static let shared = FavoritesManager()
    private init() {}

    @Published var slugs: Set<String> = []
    @Published var products: [Product] = []
    @Published var isLoading = false

    func fetchFavorites() async {
        guard AuthManager.shared.isLoggedIn else { return }
        isLoading = true
        defer { isLoading = false }
        do {
            let response: ArrayResponse<Product> = try await APIClient.shared.request(FavoritesEndpoint.list)
            products = response.data
            slugs    = Set(response.data.map(\.slug))
        } catch { }
    }

    func toggle(_ product: Product) async {
        if slugs.contains(product.slug) {
            await remove(product)
        } else {
            await add(product)
        }
    }

    func add(_ product: Product) async {
        slugs.insert(product.slug)
        if !products.contains(where: { $0.id == product.id }) {
            products.append(product)
        }
        do {
            let _: MessageResponse = try await APIClient.shared.request(FavoritesEndpoint.add(slug: product.slug))
        } catch {
            slugs.remove(product.slug)
            products.removeAll { $0.id == product.id }
        }
    }

    func remove(_ product: Product) async {
        slugs.remove(product.slug)
        products.removeAll { $0.id == product.id }
        do {
            let _: MessageResponse = try await APIClient.shared.request(FavoritesEndpoint.remove(slug: product.slug))
        } catch {
            slugs.insert(product.slug)
            products.append(product)
        }
    }

    func isFavorite(_ slug: String) -> Bool { slugs.contains(slug) }

    func reset() { slugs = []; products = [] }
}
