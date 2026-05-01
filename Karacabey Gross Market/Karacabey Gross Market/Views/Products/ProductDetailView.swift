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
                HStack {
                    if let p = viewModel.product {
                        Button {
                            Task { await favManager.toggle(p) }
                        } label: {
                            Image(systemName: favManager.isFavorite(p.slug) ? "heart.fill" : "heart")
                                .foregroundColor(favManager.isFavorite(p.slug) ? .red : .primary)
                        }
                    }
                    NavigationLink(destination: CartView()) { CartBarButton() }
                }
            }
        }
        .task { await viewModel.load(slug: slug) }
    }

    @ViewBuilder
    private func productContent(_ p: Product) -> some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 0) {
                // Parallax Image
                GeometryReader { geo in
                    let minY = geo.frame(in: .global).minY
                    let isScrollingDown = minY < 0
                    let height = isScrollingDown ? 350 : 350 + minY
                    let offset = isScrollingDown ? -minY : -minY

                    productImage(p)
                        .frame(width: geo.size.width, height: max(0, height))
                        .offset(y: offset)
                }
                .frame(height: 350)
                .zIndex(1)

                // Content
                VStack(alignment: .leading, spacing: 20) {
                    // Header Area
                    VStack(alignment: .leading, spacing: 8) {
                        if let brand = p.brand {
                            Text(brand.uppercased())
                                .font(.system(size: 13, weight: .bold, design: .rounded))
                                .foregroundColor(.kgmOrange)
                                .padding(.horizontal, 10)
                                .padding(.vertical, 4)
                                .background(Color.kgmOrange.opacity(0.1))
                                .cornerRadius(8)
                        }
                        
                        Text(p.name)
                            .font(.system(size: 22, weight: .bold, design: .rounded))
                            .foregroundColor(.primary)
                            .lineLimit(3)
                        
                        // Price & Badges
                        HStack(alignment: .bottom, spacing: 12) {
                            Text(p.displayPrice)
                                .font(.system(size: 28, weight: .black, design: .rounded))
                                .foregroundColor(.primary)
                            
                            if p.hasDiscount, let pct = p.discountPercent {
                                Text("%\(pct) İNDİRİM")
                                    .font(.system(size: 12, weight: .bold, design: .rounded))
                                    .foregroundColor(.white)
                                    .padding(.horizontal, 8).padding(.vertical, 4)
                                    .background(Color.green)
                                    .cornerRadius(6)
                            }
                        }
                    }

                    Divider()

                    // Stock Info
                    HStack(spacing: 12) {
                        Circle()
                            .fill(p.isInStock ? Color.green.opacity(0.2) : Color.red.opacity(0.2))
                            .frame(width: 40, height: 40)
                            .overlay(
                                Image(systemName: p.isInStock ? "checkmark" : "xmark")
                                    .foregroundColor(p.isInStock ? .green : .red)
                                    .font(.system(size: 18, weight: .bold))
                            )
                        
                        VStack(alignment: .leading, spacing: 2) {
                            Text(p.isInStock ? "Stokta Var" : "Tükendi")
                                .font(.system(size: 15, weight: .bold))
                                .foregroundColor(p.isInStock ? .green : .red)
                            Text(p.isInStock ? "Hemen teslimata uygun" : "Şu an temin edilemiyor")
                                .font(.system(size: 13))
                                .foregroundColor(.secondary)
                        }
                    }
                    .padding()
                    .frame(maxWidth: .infinity, alignment: .leading)
                    .background(Color(UIColor.secondarySystemBackground))
                    .cornerRadius(12)

                    // Description
                    if let desc = p.description, !desc.isEmpty {
                        VStack(alignment: .leading, spacing: 12) {
                            Text("Ürün Açıklaması")
                                .font(.system(size: 18, weight: .bold, design: .rounded))
                            
                            Text(desc)
                                .font(.system(size: 15, weight: .regular))
                                .foregroundColor(.secondary)
                                .lineSpacing(6)
                        }
                    }

                    // Categories
                    if let cats = p.categories, !cats.isEmpty {
                        VStack(alignment: .leading, spacing: 12) {
                            Text("Kategoriler")
                                .font(.system(size: 16, weight: .semibold, design: .rounded))

                            ScrollView(.horizontal, showsIndicators: false) {
                                HStack(spacing: 10) {
                                    ForEach(cats) { cat in
                                        NavigationLink(destination: ProductsView(initialCategory: cat.slug)) {
                                            Text(cat.name)
                                                .font(.system(size: 13, weight: .medium))
                                                .padding(.horizontal, 16)
                                                .padding(.vertical, 8)
                                                .background(Color(UIColor.tertiarySystemBackground))
                                                .foregroundColor(.primary)
                                                .overlay(
                                                    RoundedRectangle(cornerRadius: 20)
                                                        .stroke(Color(UIColor.separator), lineWidth: 1)
                                                )
                                                .cornerRadius(20)
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // Reviews Section
                    VStack(alignment: .leading, spacing: 12) {
                        HStack {
                            Text("Değerlendirmeler")
                                .font(.system(size: 16, weight: .semibold, design: .rounded))
                            Spacer()
                            NavigationLink(destination: ProductReviewsView(productSlug: p.slug)) {
                                HStack(spacing: 4) {
                                    Text("Tümü")
                                        .font(.system(size: 12, weight: .semibold))
                                    Image(systemName: "chevron.right")
                                        .font(.system(size: 10, weight: .semibold))
                                }
                                .foregroundColor(.kgmOrange)
                            }
                        }

                        HStack(spacing: 8) {
                            VStack(alignment: .center, spacing: 4) {
                                Text("4.5")
                                    .font(.system(size: 20, weight: .bold))
                                    .foregroundColor(.kgmOrange)
                                HStack(spacing: 2) {
                                    ForEach(0..<5, id: \.self) { i in
                                        Image(systemName: i < 4 ? "star.fill" : "star")
                                            .font(.system(size: 10))
                                            .foregroundColor(.kgmOrange)
                                    }
                                }
                            }
                            .frame(width: 60)

                            VStack(alignment: .leading, spacing: 4) {
                                Text("127 Değerlendirme")
                                    .font(.system(size: 12, weight: .semibold))
                                    .foregroundColor(.primary)
                                NavigationLink(destination: ProductReviewsView(productSlug: p.slug)) {
                                    Text("Değerlendirme Yap")
                                        .font(.system(size: 12, weight: .semibold))
                                        .foregroundColor(.kgmOrange)
                                }
                            }

                            Spacer()
                        }
                        .padding()
                        .background(Color(UIColor.secondarySystemBackground))
                        .cornerRadius(10)
                    }

                    Spacer().frame(height: 20) // Bottom padding for sticky bar
                }
                .padding(20)
                .background(Color(UIColor.systemBackground))
                .cornerRadius(24, corners: [.topLeft, .topRight])
                .offset(y: -24)
                .zIndex(2)
            }
        }
        .ignoresSafeArea(edges: .top)
        .safeAreaInset(edge: .bottom) {
            stickyBottomBar(p)
        }
    }

    private func stickyBottomBar(_ p: Product) -> some View {
        VStack(spacing: 0) {
            Divider()
            HStack(spacing: 16) {
                // Quantity Stepper
                HStack(spacing: 16) {
                    Button { if qty > 1 { UIImpactFeedbackGenerator(style: .light).impactOccurred(); qty -= 1 } } label: {
                        Image(systemName: "minus.circle.fill")
                            .font(.system(size: 28))
                            .foregroundColor(qty > 1 ? .kgmOrange : Color(UIColor.systemGray4))
                    }
                    
                    Text("\(qty)")
                        .font(.system(size: 18, weight: .bold, design: .rounded))
                        .frame(width: 30)
                    
                    Button { UIImpactFeedbackGenerator(style: .light).impactOccurred(); qty += 1 } label: {
                        Image(systemName: "plus.circle.fill")
                            .font(.system(size: 28))
                            .foregroundColor(.kgmOrange)
                    }
                }
                .padding(.horizontal, 12)
                .padding(.vertical, 8)
                .background(Color(UIColor.secondarySystemBackground))
                .cornerRadius(30)

                // Add to Cart Button
                Button {
                    UIImpactFeedbackGenerator(style: .medium).impactOccurred()
                    Task {
                        await cartManager.addItem(slug: p.slug, quantity: qty)
                        withAnimation { addedToCart = true }
                        DispatchQueue.main.asyncAfter(deadline: .now() + 2) {
                            withAnimation { addedToCart = false }
                        }
                    }
                } label: {
                    HStack {
                        Image(systemName: addedToCart ? "checkmark" : "cart.fill")
                        Text(addedToCart ? "Eklendi" : "Sepete Ekle")
                    }
                    .font(.system(size: 16, weight: .bold, design: .rounded))
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 16)
                    .background(addedToCart ? Color.green : (p.isInStock ? Color.kgmOrange : Color.gray))
                    .foregroundColor(.white)
                    .cornerRadius(30)
                    .shadow(color: (addedToCart ? Color.green : Color.kgmOrange).opacity(0.3), radius: 10, y: 5)
                }
                .disabled(!p.isInStock || addedToCart)
            }
            .padding(.horizontal, 20)
            .padding(.top, 16)
            .padding(.bottom, UIApplication.shared.windows.first?.safeAreaInsets.bottom ?? 20)
            .background(.ultraThinMaterial)
        }
    }

    private func productImage(_ p: Product) -> some View {
        ZStack {
            Color.white // Bright background for products
            if let urlStr = p.imageUrl, let url = URL(string: urlStr) {
                AsyncImage(url: url) { phase in
                    switch phase {
                    case .success(let img): 
                        img.resizable().scaledToFit().padding(40)
                    case .empty:
                        ProgressView().tint(.kgmOrange)
                    default: 
                        Image(systemName: "photo").font(.system(size: 56)).foregroundColor(Color(UIColor.systemGray4))
                    }
                }
            } else {
                Image(systemName: "photo").font(.system(size: 56)).foregroundColor(Color(UIColor.systemGray4))
            }
        }
    }
}

// Helper for specific corner rounding
extension View {
    func cornerRadius(_ radius: CGFloat, corners: UIRectCorner) -> some View {
        clipShape( RoundedCorner(radius: radius, corners: corners) )
    }
}

struct RoundedCorner: Shape {
    var radius: CGFloat = .infinity
    var corners: UIRectCorner = .allCorners

    func path(in rect: CGRect) -> Path {
        let path = UIBezierPath(roundedRect: rect, byRoundingCorners: corners, cornerRadii: CGSize(width: radius, height: radius))
        return Path(path.cgPath)
    }
}

