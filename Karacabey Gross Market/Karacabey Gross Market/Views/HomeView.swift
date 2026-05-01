import SwiftUI
import Combine

// MARK: - HomeView

struct HomeView: View {
    @StateObject private var viewModel = HomeViewModel()
    @State private var navigateToSearch = false

    var body: some View {
        ScrollView(showsIndicators: false) {
            VStack(alignment: .leading, spacing: 0) {

                // ── Hero Carousel (Reels/Banner style)
                HeroBannerCarousel()
                    .padding(.top, 8)

                // ── Search Bar
                SearchBarButton(isActive: $navigateToSearch)
                    .padding(.horizontal, 16)
                    .padding(.top, 16)
                    .navigationDestination(isPresented: $navigateToSearch) {
                        SearchView()
                    }

                // ── Stories (API)
                if !viewModel.stories.isEmpty {
                    StoriesRow(stories: viewModel.stories)
                        .padding(.top, 24)
                } else if !viewModel.categories.isEmpty {
                    // Fallback to categories if no stories
                    SectionHeader(title: "Kategoriler", destination: AnyView(CategoriesView()))
                        .padding(.top, 24)
                    StoriesRowCategoriesFallback(categories: viewModel.categories)
                        .padding(.top, 8)
                }

                // ── Campaign Cards
                SectionHeader(title: "Kampanyalar", destination: AnyView(ProductsView()))
                    .padding(.top, 28)
                CampaignCardsRow()
                    .padding(.top, 8)

                // ── Featured Products
                SectionHeader(title: "Öne Çıkan Ürünler", destination: AnyView(ProductsView()))
                    .padding(.top, 28)

                if viewModel.isLoading {
                    HStack { Spacer(); ProgressView().tint(.kgmOrange); Spacer() }
                        .padding(.vertical, 24)
                } else if let err = viewModel.errorMessage {
                    ErrorRetryView(message: err) { Task { await viewModel.load() } }
                        .padding()
                } else {
                    FeaturedProductsRow(products: viewModel.featuredProducts)
                        .padding(.top, 8)
                }

                Spacer().frame(height: 32)
            }
        }
        .background(Color(.systemGroupedBackground))
        .navigationBarTitleDisplayMode(.inline)
        .toolbar { HomeToolbar() }
        .task { await viewModel.load() }
    }
}

// MARK: - Toolbar

private struct HomeToolbar: ToolbarContent {
    var body: some ToolbarContent {
        ToolbarItem(placement: .navigationBarLeading) {
            HStack(spacing: 4) {
                Image(systemName: "storefront.fill")
                    .foregroundColor(.kgmOrange)
                    .font(.system(size: 18, weight: .semibold))
                Text("KGM")
                    .font(.poppins(weight: .bold, size: 20))
                    .foregroundColor(.kgmDarkGray)
            }
        }
        ToolbarItem(placement: .navigationBarTrailing) {
            NavigationLink(destination: CartView()) {
                CartBarButton()
            }
        }
    }
}

// MARK: - Hero Banner Carousel

private struct HeroBannerCarousel: View {
    @State private var currentIndex = 0
    let timer = Timer.publish(every: 4, on: .main, in: .common).autoconnect()

    let banners: [BannerData] = [
        BannerData(
            title: "Toptan Fiyatına\nAlışveriş",
            subtitle: "11.840'dan fazla ürün",
            icon: "cart.badge.plus",
            colors: [Color(hex: "#FF7A00"), Color(hex: "#CC3300")]
        ),
        BannerData(
            title: "Yeni Kampanyalar\nSizi Bekliyor",
            subtitle: "Fırsatları kaçırmayın!",
            icon: "tag.fill",
            colors: [Color(hex: "#6C5CE7"), Color(hex: "#341f97")]
        ),
        BannerData(
            title: "Hızlı & Güvenli\nTeslimat",
            subtitle: "Aynı gün kargo imkânı",
            icon: "shippingbox.fill",
            colors: [Color(hex: "#00B894"), Color(hex: "#006266")]
        ),
        BannerData(
            title: "Toplu Siparişe\nÖzel İndirim",
            subtitle: "İşletmeniz için avantajlı fiyatlar",
            icon: "building.2.fill",
            colors: [Color(hex: "#E17055"), Color(hex: "#c0392b")]
        ),
    ]

    var body: some View {
        ZStack(alignment: .bottomTrailing) {
            TabView(selection: $currentIndex) {
                ForEach(banners.indices, id: \.self) { i in
                    BannerCard(banner: banners[i]).tag(i)
                }
            }
            .tabViewStyle(.page(indexDisplayMode: .never))
            .frame(height: 190)

            // Custom page dots
            HStack(spacing: 5) {
                ForEach(banners.indices, id: \.self) { i in
                    Capsule()
                        .fill(i == currentIndex ? Color.white : Color.white.opacity(0.45))
                        .frame(width: i == currentIndex ? 18 : 6, height: 6)
                        .animation(.spring(response: 0.3), value: currentIndex)
                }
            }
            .padding(.trailing, 20)
            .padding(.bottom, 14)
        }
        .onReceive(timer) { _ in
            withAnimation(.easeInOut(duration: 0.5)) {
                currentIndex = (currentIndex + 1) % banners.count
            }
        }
    }
}

struct BannerData {
    let title: String
    let subtitle: String
    let icon: String
    let colors: [Color]
}

private struct BannerCard: View {
    let banner: BannerData
    var body: some View {
        ZStack(alignment: .leading) {
            LinearGradient(colors: banner.colors, startPoint: .topLeading, endPoint: .bottomTrailing)

            // Background icon
            HStack {
                Spacer()
                Image(systemName: banner.icon)
                    .font(.system(size: 90, weight: .thin))
                    .foregroundColor(.white.opacity(0.18))
                    .offset(x: 16, y: 10)
            }

            VStack(alignment: .leading, spacing: 6) {
                Text(banner.title)
                    .font(.poppins(weight: .bold, size: 22))
                    .foregroundColor(.white)
                    .lineSpacing(2)

                Text(banner.subtitle)
                    .font(.poppins(weight: .medium, size: 13))
                    .foregroundColor(.white.opacity(0.85))

                NavigationLink(destination: ProductsView()) {
                    HStack(spacing: 4) {
                        Text("Keşfet")
                            .font(.poppins(weight: .bold, size: 12))
                        Image(systemName: "arrow.right")
                            .font(.system(size: 10, weight: .bold))
                    }
                    .foregroundColor(banner.colors.first ?? .kgmOrange)
                    .padding(.horizontal, 14).padding(.vertical, 7)
                    .background(.white)
                    .cornerRadius(20)
                }
                .padding(.top, 4)
            }
            .padding(.leading, 24)
        }
        .clipShape(RoundedRectangle(cornerRadius: 20))
        .padding(.horizontal, 16)
        .shadow(color: banner.colors.first?.opacity(0.35) ?? .clear, radius: 12, x: 0, y: 6)
    }
}

// MARK: - Search Bar Button

private struct SearchBarButton: View {
    @Binding var isActive: Bool
    var body: some View {
        Button { isActive = true } label: {
            HStack(spacing: 10) {
                Image(systemName: "magnifyingglass")
                    .foregroundColor(.kgmOrange)
                    .font(.system(size: 16, weight: .semibold))
                Text("Ürün ara…")
                    .font(.poppins(weight: .regular, size: 14))
                    .foregroundColor(.kgmGray)
                Spacer()
                Text("Ara")
                    .font(.poppins(weight: .semibold, size: 12))
                    .foregroundColor(.white)
                    .padding(.horizontal, 12).padding(.vertical, 5)
                    .background(Color.kgmOrange)
                    .cornerRadius(10)
            }
            .padding(.horizontal, 14).padding(.vertical, 12)
            .background(Color(.secondarySystemBackground))
            .cornerRadius(14)
            .shadow(color: .black.opacity(0.04), radius: 4, x: 0, y: 2)
        }
        .buttonStyle(.plain)
    }
}

// MARK: - Stories Row (API)

struct StoriesRow: View {
    let stories: [Story]

    var body: some View {
        ScrollView(.horizontal, showsIndicators: false) {
            HStack(spacing: 14) {
                ForEach(stories) { story in
                    NavigationLink(destination: getDestination(for: story)) {
                        StoryBubbleAPI(story: story)
                    }
                    .buttonStyle(.plain)
                }
            }
            .padding(.horizontal, 16)
            .padding(.vertical, 4)
        }
    }

    private func getDestination(for story: Story) -> AnyView {
        if let slug = story.categorySlug, !slug.isEmpty {
            return AnyView(ProductsView(initialCategory: slug))
        }
        return AnyView(ProductsView())
    }
}

struct StoryBubbleAPI: View {
    let story: Story

    var body: some View {
        VStack(spacing: 6) {
            ZStack {
                // Gradient ring
                Circle()
                    .stroke(
                        LinearGradient(
                            colors: [Color(hex: story.gradientStart ?? "#FF7A00"), Color(hex: story.gradientEnd ?? "#FF3300")],
                            startPoint: .topLeading,
                            endPoint: .bottomTrailing
                        ),
                        lineWidth: 2.5
                    )
                    .frame(width: 66, height: 66)

                Circle()
                    .fill(Color(.secondarySystemBackground))
                    .frame(width: 60, height: 60)

                if let imageUrl = story.imageUrl, let url = URL(string: imageUrl) {
                    AsyncImage(url: url) { phase in
                        switch phase {
                        case .success(let img): 
                            img.resizable().scaledToFill().clipShape(Circle())
                        default: 
                            Image(systemName: "photo").foregroundColor(.gray)
                        }
                    }
                    .frame(width: 60, height: 60)
                } else {
                    Image(systemName: story.icon ?? "tag.fill")
                        .font(.system(size: 22, weight: .medium))
                        .foregroundStyle(
                            LinearGradient(
                                colors: [Color(hex: story.gradientStart ?? "#FF7A00"), Color(hex: story.gradientEnd ?? "#FF3300")],
                                startPoint: .topLeading,
                                endPoint: .bottomTrailing
                            )
                        )
                }
            }

            Text(story.title)
                .font(.poppins(weight: .medium, size: 10))
                .foregroundColor(.kgmDarkGray)
                .lineLimit(2)
                .multilineTextAlignment(.center)
                .frame(width: 66)
        }
    }
}

// MARK: - Stories Row Fallback (Categories)

struct StoriesRowCategoriesFallback: View {
    let categories: [Category]

    var body: some View {
        ScrollView(.horizontal, showsIndicators: false) {
            HStack(spacing: 14) {
                // "Tümü" bubble
                NavigationLink(destination: ProductsView()) {
                    StoryBubble(
                        name: "Tümü",
                        icon: "square.grid.2x2.fill",
                        colors: [.kgmOrange, Color(hex: "#FF3300")],
                        hasNewBadge: false
                    )
                }
                .buttonStyle(.plain)

                ForEach(categories.prefix(14)) { cat in
                    NavigationLink(destination: ProductsView(initialCategory: cat.slug)) {
                        StoryBubble(
                            name: cat.name,
                            icon: iconForCategory(cat.name),
                            colors: gradientForCategory(cat.name),
                            hasNewBadge: false
                        )
                    }
                    .buttonStyle(.plain)
                }
            }
            .padding(.horizontal, 16)
            .padding(.vertical, 4)
        }
    }

    private func iconForCategory(_ name: String) -> String {
        let n = name.lowercased()
        if n.contains("içecek") || n.contains("su")       { return "drop.fill" }
        if n.contains("temizlik") || n.contains("deterjan") { return "bubbles.and.sparkles.fill" }
        if n.contains("gıda") || n.contains("temel")      { return "basket.fill" }
        if n.contains("bebek")                             { return "figure.and.child.holdinghands" }
        if n.contains("kişisel") || n.contains("bakım")   { return "face.smiling.fill" }
        if n.contains("kahve") || n.contains("çay")       { return "cup.and.saucer.fill" }
        if n.contains("şeker") || n.contains("çikolata")  { return "birthday.cake.fill" }
        if n.contains("makarna") || n.contains("un")      { return "fork.knife" }
        if n.contains("konserve") || n.contains("salça")  { return "cylinder.split.1x2.fill" }
        if n.contains("yağ") || n.contains("zeytinyağı")  { return "drop.halffull" }
        if n.contains("sabun") || n.contains("şampuan")   { return "bubbles.and.sparkles" }
        if n.contains("kâğıt") || n.contains("kagit")    { return "doc.fill" }
        return "tag.fill"
    }

    private func gradientForCategory(_ name: String) -> [Color] {
        let palettes: [[Color]] = [
            [Color(hex: "#FF7A00"), Color(hex: "#FF3300")],
            [Color(hex: "#6C5CE7"), Color(hex: "#a29bfe")],
            [Color(hex: "#00B894"), Color(hex: "#00CEC9")],
            [Color(hex: "#E17055"), Color(hex: "#d63031")],
            [Color(hex: "#0984e3"), Color(hex: "#74b9ff")],
            [Color(hex: "#fd79a8"), Color(hex: "#e84393")],
            [Color(hex: "#FDCB6E"), Color(hex: "#e17055")],
        ]
        let idx = abs(name.hashValue) % palettes.count
        return palettes[idx]
    }
}

struct StoryBubble: View {
    let name: String
    let icon: String
    let colors: [Color]
    let hasNewBadge: Bool

    var body: some View {
        VStack(spacing: 6) {
            ZStack {
                // Gradient ring
                Circle()
                    .stroke(
                        LinearGradient(colors: colors, startPoint: .topLeading, endPoint: .bottomTrailing),
                        lineWidth: 2.5
                    )
                    .frame(width: 66, height: 66)

                Circle()
                    .fill(Color(.secondarySystemBackground))
                    .frame(width: 60, height: 60)

                Image(systemName: icon)
                    .font(.system(size: 22, weight: .medium))
                    .foregroundStyle(
                        LinearGradient(colors: colors, startPoint: .topLeading, endPoint: .bottomTrailing)
                    )
            }
            .overlay(alignment: .topTrailing) {
                if hasNewBadge {
                    Circle().fill(Color.green).frame(width: 12, height: 12)
                        .offset(x: 2, y: 2)
                }
            }

            Text(name)
                .font(.poppins(weight: .medium, size: 10))
                .foregroundColor(.kgmDarkGray)
                .lineLimit(2)
                .multilineTextAlignment(.center)
                .frame(width: 66)
        }
    }
}

// MARK: - Campaign Cards Row

private struct CampaignCardsRow: View {
    let campaigns: [CampaignItem] = [
        CampaignItem(title: "%15 İndirim", subtitle: "Temizlik ürünlerinde", icon: "bubbles.and.sparkles.fill",
                     colors: [Color(hex: "#6C5CE7"), Color(hex: "#a29bfe")], category: "temizlik"),
        CampaignItem(title: "2 Al 1 Öde", subtitle: "Seçili içeceklerde", icon: "drop.fill",
                     colors: [Color(hex: "#0984e3"), Color(hex: "#74b9ff")], category: "icecek"),
        CampaignItem(title: "Toplu Alım", subtitle: "Koli alımlarına indirim", icon: "shippingbox.fill",
                     colors: [Color(hex: "#00B894"), Color(hex: "#006266")], category: nil),
        CampaignItem(title: "Yeni Ürünler", subtitle: "Haftalık yenilikler", icon: "sparkles",
                     colors: [Color(hex: "#E17055"), Color(hex: "#c0392b")], category: nil),
    ]

    var body: some View {
        ScrollView(.horizontal, showsIndicators: false) {
            HStack(spacing: 12) {
                ForEach(campaigns) { campaign in
                    NavigationLink(destination: ProductsView(initialCategory: campaign.category)) {
                        CampaignCard(campaign: campaign)
                    }
                    .buttonStyle(.plain)
                }
            }
            .padding(.horizontal, 16)
            .padding(.vertical, 4)
        }
    }
}

struct CampaignItem: Identifiable {
    let id = UUID()
    let title: String
    let subtitle: String
    let icon: String
    let colors: [Color]
    let category: String?
}

private struct CampaignCard: View {
    let campaign: CampaignItem
    var body: some View {
        ZStack(alignment: .bottomLeading) {
            LinearGradient(colors: campaign.colors, startPoint: .topLeading, endPoint: .bottomTrailing)
                .frame(width: 160, height: 100)
                .cornerRadius(16)

            Image(systemName: campaign.icon)
                .font(.system(size: 44, weight: .thin))
                .foregroundColor(.white.opacity(0.2))
                .frame(maxWidth: .infinity, maxHeight: .infinity, alignment: .topTrailing)
                .padding(.trailing, 10)
                .padding(.top, 10)

            VStack(alignment: .leading, spacing: 2) {
                Text(campaign.title)
                    .font(.poppins(weight: .bold, size: 14))
                    .foregroundColor(.white)
                Text(campaign.subtitle)
                    .font(.poppins(weight: .regular, size: 10))
                    .foregroundColor(.white.opacity(0.85))
            }
            .padding(12)
        }
        .frame(width: 160, height: 100)
        .shadow(color: campaign.colors.first?.opacity(0.3) ?? .clear, radius: 8, x: 0, y: 4)
    }
}

// MARK: - Featured Products Row

private struct FeaturedProductsRow: View {
    let products: [Product]
    var body: some View {
        ScrollView(.horizontal, showsIndicators: false) {
            HStack(spacing: 14) {
                ForEach(products) { product in
                    NavigationLink(destination: ProductDetailView(slug: product.slug)) {
                        ProductCard(product: product)
                            .frame(width: 158)
                    }
                    .buttonStyle(.plain)
                }
            }
            .padding(.horizontal, 16)
            .padding(.vertical, 4)
        }
    }
}

// MARK: - Section Header

struct SectionHeader: View {
    let title: String
    let destination: AnyView
    var body: some View {
        HStack {
            Text(title)
                .font(.poppins(weight: .bold, size: 18))
                .foregroundColor(.kgmDarkGray)
            Spacer()
            NavigationLink(destination: destination) {
                HStack(spacing: 2) {
                    Text("Tümü")
                        .font(.poppins(weight: .semibold, size: 13))
                    Image(systemName: "chevron.right")
                        .font(.system(size: 11, weight: .semibold))
                }
                .foregroundColor(.kgmOrange)
            }
        }
        .padding(.horizontal, 16)
    }
}

// MARK: - Error Retry

private struct ErrorRetryView: View {
    let message: String
    let retry: () -> Void
    var body: some View {
        VStack(spacing: 12) {
            Image(systemName: "wifi.slash")
                .font(.system(size: 36))
                .foregroundColor(.kgmGray)
            Text(message)
                .font(.poppins(weight: .medium, size: 13))
                .foregroundColor(.secondary)
                .multilineTextAlignment(.center)
            Button("Tekrar Dene", action: retry)
                .font(.poppins(weight: .bold, size: 14))
                .foregroundColor(.white)
                .padding(.horizontal, 24).padding(.vertical, 10)
                .background(Color.kgmOrange)
                .cornerRadius(12)
        }
        .frame(maxWidth: .infinity)
    }
}
