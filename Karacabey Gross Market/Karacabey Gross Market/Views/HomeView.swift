import SwiftUI

struct HomeView: View {
    @StateObject private var viewModel = HomeViewModel()
    @EnvironmentObject private var cartManager: CartManager
    @State private var searchText = ""
    @State private var navigateToSearch = false

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 24) {

                // ── Hero Banner
                ZStack(alignment: .leading) {
                    RoundedRectangle(cornerRadius: 20)
                        .fill(LinearGradient(
                            colors: [.kgmOrange, Color(hex: "#FF5500")],
                            startPoint: .topLeading, endPoint: .bottomTrailing
                        ))
                        .frame(height: 160)
                    VStack(alignment: .leading, spacing: 6) {
                        Text("Toptan Fiyatına")
                            .font(.poppins(weight: .bold, size: 22))
                            .foregroundColor(.white)
                        Text("Güvenle Alışveriş")
                            .font(.poppins(weight: .bold, size: 22))
                            .foregroundColor(.white)
                        Text("11.840+ ürün")
                            .font(.poppins(weight: .medium, size: 13))
                            .foregroundColor(.white.opacity(0.85))
                    }
                    .padding(.leading, 24)

                    HStack {
                        Spacer()
                        Image(systemName: "cart.badge.plus")
                            .font(.system(size: 64))
                            .foregroundColor(.white.opacity(0.25))
                            .padding(.trailing, 20)
                    }
                }
                .padding(.horizontal)

                // ── Search bar (navigates to ProductsView)
                Button {
                    navigateToSearch = true
                } label: {
                    HStack {
                        Image(systemName: "magnifyingglass").foregroundColor(.kgmGray)
                        Text("Ürün ara…").foregroundColor(.kgmGray)
                        Spacer()
                    }
                    .padding()
                    .background(Color(UIColor.secondarySystemBackground))
                    .cornerRadius(12)
                }
                .buttonStyle(.plain)
                .padding(.horizontal)
                .navigationDestination(isPresented: $navigateToSearch) {
                    ProductsView()
                }

                // ── Categories
                if !viewModel.categories.isEmpty {
                    VStack(alignment: .leading, spacing: 12) {
                        SectionHeader(title: "Kategoriler", destination: AnyView(CategoriesView()))
                        ScrollView(.horizontal, showsIndicators: false) {
                            HStack(spacing: 12) {
                                ForEach(viewModel.categories) { cat in
                                    NavigationLink(destination: ProductsView(initialCategory: cat.slug)) {
                                        CategoryChip(category: cat)
                                    }
                                }
                            }
                            .padding(.horizontal)
                        }
                    }
                }

                // ── Featured Products
                VStack(alignment: .leading, spacing: 12) {
                    SectionHeader(title: "Öne Çıkan Ürünler", destination: AnyView(ProductsView()))
                    if viewModel.isLoading {
                        HStack { Spacer(); ProgressView(); Spacer() }.padding()
                    } else if let err = viewModel.errorMessage {
                        Text(err).foregroundColor(.secondary).padding()
                    } else {
                        ScrollView(.horizontal, showsIndicators: false) {
                            HStack(spacing: 16) {
                                ForEach(viewModel.featuredProducts) { product in
                                    NavigationLink(destination: ProductDetailView(slug: product.slug)) {
                                        ProductCard(product: product)
                                    }
                                    .buttonStyle(.plain)
                                }
                            }
                            .padding(.horizontal)
                        }
                    }
                }
            }
            .padding(.vertical)
        }
        .background(Color(UIColor.systemGroupedBackground))
        .navigationTitle("KGM")
        .navigationBarTitleDisplayMode(.inline)
        .toolbar {
            ToolbarItem(placement: .topBarTrailing) {
                NavigationLink(destination: CartView()) {
                    CartBarButton()
                }
            }
        }
        .task { await viewModel.load() }
    }
}

// MARK: - Section Header
struct SectionHeader: View {
    let title: String
    let destination: AnyView
    var body: some View {
        HStack {
            Text(title).font(.poppins(weight: .bold, size: 18))
            Spacer()
            NavigationLink(destination: destination) {
                Text("Tümü").font(.poppins(weight: .medium, size: 14)).foregroundColor(.kgmOrange)
            }
        }
        .padding(.horizontal)
    }
}

// MARK: - Category Chip
struct CategoryChip: View {
    let category: Category
    var body: some View {
        VStack(spacing: 8) {
            Circle()
                .fill(Color.kgmOrange.opacity(0.12))
                .frame(width: 56, height: 56)
                .overlay(
                    Image(systemName: "tag.fill")
                        .foregroundColor(.kgmOrange)
                        .font(.system(size: 22))
                )
            Text(category.name)
                .font(.poppins(weight: .medium, size: 11))
                .foregroundColor(.kgmDarkGray)
                .lineLimit(2)
                .multilineTextAlignment(.center)
                .frame(width: 64)
        }
    }
}
