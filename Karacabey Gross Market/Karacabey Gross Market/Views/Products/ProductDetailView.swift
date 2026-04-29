import SwiftUI

struct ProductDetailView: View {
    let slug: String
    @StateObject private var viewModel = ProductDetailViewModel()
    @EnvironmentObject private var cartManager: CartManager
    @EnvironmentObject private var favManager: FavoritesManager
    @State private var qty = 1
    @State private var addedToCart = false

    var body: some View {
        Group {
            if viewModel.isLoading {
                ProgressView("Yükleniyor…").frame(maxWidth: .infinity, maxHeight: .infinity)
            } else if let p = viewModel.product {
                productContent(p)
            } else if let err = viewModel.errorMessage {
                VStack(spacing: 12) {
                    Image(systemName: "exclamationmark.triangle").font(.largeTitle).foregroundColor(.orange)
                    Text(err).foregroundColor(.secondary)
                }
                .frame(maxWidth: .infinity, maxHeight: .infinity)
            }
        }
        .navigationBarTitleDisplayMode(.inline)
        .toolbar {
            ToolbarItem(placement: .topBarTrailing) {
                NavigationLink(destination: CartView()) { CartBarButton() }
            }
        }
        .task { await viewModel.load(slug: slug) }
    }

    @ViewBuilder
    private func productContent(_ p: Product) -> some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 0) {

                // Image
                productImage(p)

                VStack(alignment: .leading, spacing: 16) {
                    // Brand + Name
                    if let brand = p.brand {
                        Text(brand.uppercased())
                            .font(.poppins(weight: .medium, size: 12))
                            .foregroundColor(.kgmGray)
                    }
                    Text(p.name)
                        .font(.poppins(weight: .bold, size: 20))
                        .foregroundColor(.kgmDarkGray)

                    // Price
                    HStack(alignment: .bottom, spacing: 8) {
                        Text(p.displayPrice)
                            .font(.poppins(weight: .bold, size: 26))
                            .foregroundColor(.kgmOrange)
                        if p.hasDiscount, let pct = p.discountPercent {
                            Text("%\(pct) indirim")
                                .font(.poppins(weight: .medium, size: 13))
                                .foregroundColor(.white)
                                .padding(.horizontal, 8).padding(.vertical, 3)
                                .background(Color.green)
                                .cornerRadius(8)
                        }
                    }

                    // Stock badge
                    Label(p.isInStock ? "Stokta Var" : "Tükendi",
                          systemImage: p.isInStock ? "checkmark.circle.fill" : "xmark.circle.fill")
                        .font(.poppins(weight: .medium, size: 13))
                        .foregroundColor(p.isInStock ? .green : .red)

                    Divider()

                    // Description
                    if let desc = p.description, !desc.isEmpty {
                        Text(desc)
                            .font(.poppins(weight: .regular, size: 14))
                            .foregroundColor(.kgmGray)
                            .lineSpacing(4)
                    }

                    // Category chips
                    if let cats = p.categories, !cats.isEmpty {
                        ScrollView(.horizontal, showsIndicators: false) {
                            HStack {
                                ForEach(cats) { cat in
                                    NavigationLink(destination: ProductsView(initialCategory: cat.slug)) {
                                        Text(cat.name)
                                            .font(.poppins(weight: .medium, size: 12))
                                            .padding(.horizontal, 12).padding(.vertical, 6)
                                            .background(Color.kgmOrange.opacity(0.1))
                                            .foregroundColor(.kgmOrange)
                                            .cornerRadius(20)
                                    }
                                }
                            }
                        }
                    }

                    Divider()

                    // Quantity picker + Add to cart
                    HStack(spacing: 16) {
                        // Stepper
                        HStack(spacing: 0) {
                            Button { if qty > 1 { qty -= 1 } } label: {
                                Image(systemName: "minus")
                                    .frame(width: 36, height: 36)
                                    .foregroundColor(qty > 1 ? .kgmOrange : .gray)
                            }
                            Text("\(qty)")
                                .font(.poppins(weight: .bold, size: 16))
                                .frame(width: 40)
                            Button { qty += 1 } label: {
                                Image(systemName: "plus")
                                    .frame(width: 36, height: 36)
                                    .foregroundColor(.kgmOrange)
                            }
                        }
                        .overlay(RoundedRectangle(cornerRadius: 10).stroke(Color.gray.opacity(0.25)))

                        // Add to cart button
                        Button {
                            Task {
                                await cartManager.addItem(slug: p.slug, quantity: qty)
                                addedToCart = true
                                DispatchQueue.main.asyncAfter(deadline: .now() + 1.5) { addedToCart = false }
                            }
                        } label: {
                            Label(addedToCart ? "Eklendi!" : "Sepete Ekle",
                                  systemImage: addedToCart ? "checkmark" : "cart.badge.plus")
                                .font(.poppins(weight: .bold, size: 15))
                                .frame(maxWidth: .infinity)
                                .padding(.vertical, 14)
                                .background(addedToCart ? Color.green : Color.kgmOrange)
                                .foregroundColor(.white)
                                .cornerRadius(14)
                                .animation(.easeInOut(duration: 0.2), value: addedToCart)
                        }
                        .disabled(!p.isInStock)
                    }
                }
                .padding()
            }
        }
        .navigationTitle(p.name)
        .toolbar {
            ToolbarItem(placement: .topBarTrailing) {
                Button {
                    Task { await favManager.toggle(p) }
                } label: {
                    Image(systemName: favManager.isFavorite(p.slug) ? "heart.fill" : "heart")
                        .foregroundColor(.kgmOrange)
                }
            }
        }
    }

    private func productImage(_ p: Product) -> some View {
        ZStack {
            Color(UIColor.secondarySystemBackground)
            if let urlStr = p.imageUrl, let url = URL(string: urlStr) {
                AsyncImage(url: url) { phase in
                    switch phase {
                    case .success(let img): img.resizable().scaledToFit().padding(20)
                    default: Image(systemName: "photo").font(.system(size: 56)).foregroundColor(.gray)
                    }
                }
            } else {
                Image(systemName: "photo").font(.system(size: 56)).foregroundColor(.gray)
            }
        }
        .frame(height: 280)
    }
}
