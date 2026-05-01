import SwiftUI

struct SearchView: View {
    @StateObject private var viewModel = SearchViewModel()
    @State private var searchFocused = false

    var body: some View {
        NavigationStack {
            VStack(spacing: 0) {
                // Search bar
                HStack {
                    Image(systemName: "magnifyingglass")
                        .foregroundColor(.kgmGray)
                    TextField("Ürün ara…", text: $viewModel.searchQuery)
                        .font(.poppins(weight: .regular, size: 15))
                        .onSubmit { viewModel.search(query: viewModel.searchQuery) }
                        .onChange(of: viewModel.searchQuery) { _, newValue in
                            viewModel.getSuggestions(for: newValue)
                        }
                    if !viewModel.searchQuery.isEmpty {
                        Button {
                            viewModel.searchQuery = ""
                            viewModel.results = []
                        } label: {
                            Image(systemName: "xmark.circle.fill")
                                .foregroundColor(.gray)
                        }
                    }
                }
                .padding(10)
                .background(Color(UIColor.secondarySystemBackground))
                .cornerRadius(12)
                .padding()

                if viewModel.searchQuery.isEmpty {
                    // Suggestions or recent searches
                    ScrollView {
                        VStack(alignment: .leading, spacing: 16) {
                            Text("Popüler Aramalar")
                                .font(.poppins(weight: .bold, size: 16))
                                .foregroundColor(.primary)
                                .padding(.horizontal)

                            LazyVGrid(columns: [GridItem(.flexible()), GridItem(.flexible())], spacing: 12) {
                                ForEach(["Temizlik Ürünleri", "Kahve", "Yağ", "Sabun", "Içecek", "Temel Gıda"], id: \.self) { tag in
                                    Button(action: {
                                        viewModel.searchQuery = tag
                                        viewModel.search(query: tag)
                                    }) {
                                        Text(tag)
                                            .font(.poppins(weight: .medium, size: 13))
                                            .frame(maxWidth: .infinity)
                                            .padding(.vertical, 10)
                                            .background(Color(.secondarySystemBackground))
                                            .foregroundColor(.primary)
                                            .cornerRadius(10)
                                    }
                                }
                            }
                            .padding(.horizontal)
                        }
                        .padding(.top)
                    }
                } else if viewModel.isLoading {
                    ProgressView()
                        .frame(maxWidth: .infinity, maxHeight: .infinity)
                } else if !viewModel.suggestions.isEmpty && viewModel.results.isEmpty {
                    // Show suggestions
                    List(viewModel.suggestions) { suggestion in
                        Button(action: {
                            viewModel.searchQuery = suggestion.name
                            viewModel.search(query: suggestion.name)
                        }) {
                            HStack {
                                Image(systemName: "magnifyingglass")
                                    .foregroundColor(.gray)
                                Text(suggestion.name)
                                    .foregroundColor(.primary)
                                Spacer()
                                Image(systemName: "arrow.up.left")
                                    .font(.caption)
                                    .foregroundColor(.gray)
                            }
                        }
                    }
                    .listStyle(.plain)
                } else {
                    // Search results
                    ScrollView {
                        LazyVGrid(columns: [GridItem(.flexible()), GridItem(.flexible())], spacing: 14) {
                            ForEach(viewModel.results) { product in
                                NavigationLink(destination: ProductDetailView(slug: product.slug)) {
                                    ProductCard(product: product)
                                }
                                .buttonStyle(.plain)
                            }
                        }
                        .padding()
                    }
                }
            }
            .background(Color(UIColor.systemGroupedBackground))
            .navigationTitle("Arama")
            .navigationBarTitleDisplayMode(.inline)
        }
    }
}
