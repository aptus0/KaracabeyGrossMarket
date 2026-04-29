import SwiftUI

struct ProductsView: View {
    var initialCategory: String? = nil

    @StateObject private var viewModel = ProductsViewModel()
    @State private var searchText = ""

    private let columns = [GridItem(.flexible()), GridItem(.flexible())]

    var body: some View {
        VStack(spacing: 0) {
            // Search bar
            HStack {
                Image(systemName: "magnifyingglass").foregroundColor(.kgmGray)
                TextField("Ürün ara…", text: $searchText)
                    .font(.poppins(weight: .regular, size: 15))
                    .onSubmit { Task { await viewModel.search(searchText) } }
                if !searchText.isEmpty {
                    Button { searchText = ""; Task { await viewModel.search("") } } label: {
                        Image(systemName: "xmark.circle.fill").foregroundColor(.gray)
                    }
                }
            }
            .padding(10)
            .background(Color(UIColor.secondarySystemBackground))
            .cornerRadius(12)
            .padding(.horizontal)
            .padding(.vertical, 8)

            Group {
                if viewModel.isLoading && viewModel.products.isEmpty {
                    Spacer()
                    ProgressView("Ürünler yükleniyor…")
                    Spacer()
                } else if let err = viewModel.errorMessage, viewModel.products.isEmpty {
                    emptyError(err)
                } else if viewModel.products.isEmpty {
                    emptyState
                } else {
                    productGrid
                }
            }
        }
        .background(Color(UIColor.systemGroupedBackground))
        .navigationTitle(titleText)
        .navigationBarTitleDisplayMode(.inline)
        .toolbar {
            ToolbarItem(placement: .topBarTrailing) {
                NavigationLink(destination: CartView()) { CartBarButton() }
            }
        }
        .task {
            await viewModel.load(category: initialCategory)
        }
    }

    private var titleText: String {
        viewModel.selectedCategory.map { _ in "Ürünler" } ?? "Ürünler"
    }

    private var productGrid: some View {
        ScrollView {
            LazyVGrid(columns: columns, spacing: 14) {
                ForEach(viewModel.products) { product in
                    NavigationLink(destination: ProductDetailView(slug: product.slug)) {
                        ProductCard(product: product)
                    }
                    .buttonStyle(.plain)
                }
                // Infinite scroll trigger
                if viewModel.hasMore {
                    ProgressView()
                        .frame(maxWidth: .infinity)
                        .padding()
                        .onAppear { Task { await viewModel.loadMore() } }
                }
            }
            .padding(.horizontal)
            .padding(.bottom, 20)
        }
    }

    private var emptyState: some View {
        VStack(spacing: 16) {
            Spacer()
            Image(systemName: "tray").font(.system(size: 48)).foregroundColor(.secondary)
            Text("Ürün bulunamadı").font(.poppins(weight: .medium, size: 16)).foregroundColor(.secondary)
            Spacer()
        }
    }

    private func emptyError(_ msg: String) -> some View {
        VStack(spacing: 16) {
            Spacer()
            Image(systemName: "wifi.slash").font(.system(size: 48)).foregroundColor(.orange)
            Text(msg).multilineTextAlignment(.center).foregroundColor(.secondary)
            Button("Tekrar Dene") { Task { await viewModel.load(category: initialCategory) } }
                .buttonStyle(.borderedProminent).tint(.kgmOrange)
            Spacer()
        }
        .padding()
    }
}
