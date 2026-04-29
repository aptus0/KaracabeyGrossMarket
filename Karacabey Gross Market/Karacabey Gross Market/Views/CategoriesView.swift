import SwiftUI

struct CategoriesView: View {
    @StateObject private var viewModel = CategoriesViewModel()

    let columns = [GridItem(.flexible()), GridItem(.flexible()), GridItem(.flexible())]

    var body: some View {
        Group {
            if viewModel.isLoading {
                ProgressView("Kategoriler yükleniyor…")
                    .frame(maxWidth: .infinity, maxHeight: .infinity)
            } else if let err = viewModel.errorMessage {
                VStack(spacing: 12) {
                    Image(systemName: "exclamationmark.triangle").font(.largeTitle).foregroundColor(.orange)
                    Text(err).foregroundColor(.secondary)
                    Button("Tekrar Dene") { Task { await viewModel.load() } }
                }
                .frame(maxWidth: .infinity, maxHeight: .infinity)
            } else {
                ScrollView {
                    LazyVGrid(columns: columns, spacing: 16) {
                        ForEach(viewModel.categories) { cat in
                            NavigationLink(destination: ProductsView(initialCategory: cat.slug)) {
                                CategoryGridCard(category: cat)
                            }
                            .buttonStyle(.plain)
                        }
                    }
                    .padding()
                }
            }
        }
        .navigationTitle("Kategoriler")
        .background(Color(UIColor.systemGroupedBackground))
        .task { await viewModel.load() }
    }
}

struct CategoryGridCard: View {
    let category: Category
    var body: some View {
        VStack(spacing: 10) {
            RoundedRectangle(cornerRadius: 12)
                .fill(Color.kgmOrange.opacity(0.1))
                .frame(height: 80)
                .overlay(
                    Image(systemName: "tag.fill")
                        .font(.system(size: 28))
                        .foregroundColor(.kgmOrange)
                )
            Text(category.name)
                .font(.poppins(weight: .medium, size: 12))
                .foregroundColor(.kgmDarkGray)
                .multilineTextAlignment(.center)
                .lineLimit(2)
                .padding(.horizontal, 4)
        }
        .padding(10)
        .background(Color.white)
        .cornerRadius(16)
        .shadow(color: .black.opacity(0.06), radius: 4, x: 0, y: 2)
    }
}
