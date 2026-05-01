import SwiftUI

struct ProductToolKit: View {
    let product: Product
    @State private var selectedTab: ToolSection = .features
    @EnvironmentObject private var cartManager: CartManager

    enum ToolSection {
        case features, specifications, shipping, returns

        var title: String {
            switch self {
            case .features: return "Özellikler"
            case .specifications: return "Teknik Bilgi"
            case .shipping: return "Kargo"
            case .returns: return "İadeler"
            }
        }

        var icon: String {
            switch self {
            case .features: return "sparkles"
            case .specifications: return "slider.horizontal.3"
            case .shipping: return "shippingbox.fill"
            case .returns: return "arrow.uturn.left"
            }
        }
    }

    var body: some View {
        VStack(spacing: 0) {
            // Tab Selector
            Picker("", selection: $selectedTab) {
                ForEach([ToolSection.features, .specifications, .shipping, .returns], id: \.self) { section in
                    Label(section.title, systemImage: section.icon)
                        .tag(section)
                }
            }
            .pickerStyle(.segmented)
            .padding()

            Divider()

            // Content
            ScrollView {
                VStack(alignment: .leading, spacing: 20) {
                    switch selectedTab {
                    case .features:
                        featuresContent()
                    case .specifications:
                        specificationsContent()
                    case .shipping:
                        shippingContent()
                    case .returns:
                        returnsContent()
                    }
                }
                .padding()
            }
        }
    }

    @ViewBuilder
    private func featuresContent() -> some View {
        VStack(alignment: .leading, spacing: 16) {
            featureItem(icon: "star.fill", title: "Premium Kalite", description: "Yüksek kaliteli malzeme kullanılarak üretilmiştir")
            featureItem(icon: "bolt.fill", title: "Hızlı Teslim", description: "24 saat içinde kargo çıkışı yapılır")
            featureItem(icon: "lock.fill", title: "Güvenli Ödeme", description: "256-bit şifreleme ile korunan ödeme sistemi")
            featureItem(icon: "undo", title: "30 Gün İade", description: "İade şartları olmaksızın iade edebilirsiniz")
            featureItem(icon: "phone.circle.fill", title: "Müşteri Desteği", description: "7/24 profesyonel destek ekibi")
            featureItem(icon: "shippingbox.fill", title: "Ücretsiz Kargo", description: "250 TL üzeri siparişlerde ücretsiz kargo")
        }
    }

    @ViewBuilder
    private func specificationsContent() -> some View {
        VStack(alignment: .leading, spacing: 16) {
            specRow(label: "Marka", value: product.brand ?? "Belirtilmemiş")
            specRow(label: "Ürün Kodu", value: product.barcode ?? "KGM-\(product.id)")
            specRow(label: "Stok Durumu", value: product.isInStock ? "Stokta Var" : "Tükendi")
            specRow(label: "Stok Miktarı", value: "\(product.stockQuantity) adet")
            specRow(label: "Fiyat", value: product.displayPrice)

            if let discount = product.discountPercent {
                specRow(label: "İndirim", value: "%\(discount) İndirim")
            }

            VStack(alignment: .leading, spacing: 8) {
                Text("Açıklama")
                    .font(.system(size: 14, weight: .semibold))
                    .foregroundColor(.secondary)
                Text(product.description ?? "Açıklama bulunmamaktadır")
                    .font(.system(size: 14))
                    .foregroundColor(.primary)
                    .lineSpacing(4)
            }
            .padding()
            .background(Color(UIColor.secondarySystemBackground))
            .cornerRadius(12)
        }
    }

    @ViewBuilder
    private func shippingContent() -> some View {
        VStack(alignment: .leading, spacing: 16) {
            shippingOptionCard(
                title: "Standart Kargo",
                delivery: "2-3 İş Günü",
                price: "29,99 TL",
                icon: "shippingbox",
                isPopular: false
            )

            shippingOptionCard(
                title: "Hızlı Kargo",
                delivery: "Ertesi Gün",
                price: "49,99 TL",
                icon: "bolt",
                isPopular: true
            )

            shippingOptionCard(
                title: "Sadece İstanbul",
                delivery: "3 Saat",
                price: "64,99 TL",
                icon: "map",
                isPopular: false
            )

            VStack(alignment: .leading, spacing: 12) {
                HStack {
                    Image(systemName: "info.circle.fill")
                        .foregroundColor(.kgmOrange)
                    Text("250 TL üzeri siparişlerde kargo bedeli ödenmez.")
                        .font(.system(size: 13))
                        .foregroundColor(.secondary)
                }
            }
            .padding()
            .background(Color.kgmOrange.opacity(0.1))
            .cornerRadius(12)
        }
    }

    @ViewBuilder
    private func returnsContent() -> some View {
        VStack(alignment: .leading, spacing: 16) {
            returnPolicyCard(
                title: "Koşulsuz İade",
                period: "30 Gün",
                description: "Hiçbir şart olmaksızın iade edebilirsiniz"
            )

            returnPolicyCard(
                title: "Ücretsiz Kargo",
                period: "Dahil",
                description: "İade kargo ücreti tamamen karşılanır"
            )

            returnPolicyCard(
                title: "Hızlı Geri Ödeme",
                period: "5 Gün",
                description: "İade onaylandıktan sonra 5 gün içinde para iadesi"
            )

            VStack(alignment: .leading, spacing: 12) {
                Text("İade Süreci")
                    .font(.system(size: 16, weight: .bold))

                stepCard(number: 1, text: "Ürün kusurlu veya beklendiği gibi değilse iade başvurusu yap")
                stepCard(number: 2, text: "Ücretsiz kargo etiketi al ve gönder")
                stepCard(number: 3, text: "Depo tarafından kontrol et")
                stepCard(number: 4, text: "Para iadesi işlemi başlat")
            }
        }
    }

    // MARK: - Helper Views

    private func featureItem(icon: String, title: String, description: String) -> some View {
        HStack(alignment: .top, spacing: 12) {
            Image(systemName: icon)
                .font(.system(size: 20))
                .foregroundColor(.kgmOrange)
                .frame(width: 30)

            VStack(alignment: .leading, spacing: 4) {
                Text(title)
                    .font(.system(size: 15, weight: .semibold))
                    .foregroundColor(.primary)
                Text(description)
                    .font(.system(size: 13))
                    .foregroundColor(.secondary)
                    .lineSpacing(2)
            }

            Spacer()
        }
        .padding()
        .background(Color(UIColor.secondarySystemBackground))
        .cornerRadius(12)
    }

    private func specRow(label: String, value: String) -> some View {
        HStack {
            Text(label)
                .font(.system(size: 14, weight: .medium))
                .foregroundColor(.secondary)
            Spacer()
            Text(value)
                .font(.system(size: 14, weight: .semibold))
                .foregroundColor(.primary)
        }
        .padding(.vertical, 12)
        .padding(.horizontal)
        .background(Color(UIColor.secondarySystemBackground))
        .cornerRadius(8)
    }

    private func shippingOptionCard(title: String, delivery: String, price: String, icon: String, isPopular: Bool) -> some View {
        VStack(alignment: .leading, spacing: 8) {
            HStack {
                HStack(spacing: 12) {
                    Image(systemName: icon)
                        .font(.system(size: 18))
                        .foregroundColor(.kgmOrange)

                    VStack(alignment: .leading, spacing: 2) {
                        Text(title)
                            .font(.system(size: 14, weight: .semibold))
                        Text(delivery)
                            .font(.system(size: 12))
                            .foregroundColor(.secondary)
                    }
                }

                Spacer()

                VStack(alignment: .trailing, spacing: 2) {
                    Text(price)
                        .font(.system(size: 14, weight: .bold))
                        .foregroundColor(.kgmOrange)
                }
            }

            if isPopular {
                HStack {
                    Image(systemName: "star.fill")
                        .font(.system(size: 10))
                    Text("En Popüler")
                        .font(.system(size: 11, weight: .semibold))
                }
                .foregroundColor(.white)
                .padding(.horizontal, 10)
                .padding(.vertical, 4)
                .background(Color.kgmOrange)
                .cornerRadius(6)
            }
        }
        .padding()
        .background(Color(UIColor.secondarySystemBackground))
        .cornerRadius(12)
    }

    private func returnPolicyCard(title: String, period: String, description: String) -> some View {
        VStack(alignment: .leading, spacing: 8) {
            HStack {
                VStack(alignment: .leading, spacing: 4) {
                    Text(title)
                        .font(.system(size: 15, weight: .semibold))
                    Text(period)
                        .font(.system(size: 13))
                        .foregroundColor(.kgmOrange)
                        .fontWeight(.bold)
                }

                Spacer()

                Image(systemName: "checkmark.circle.fill")
                    .font(.system(size: 24))
                    .foregroundColor(.green)
            }

            Text(description)
                .font(.system(size: 13))
                .foregroundColor(.secondary)
        }
        .padding()
        .background(Color(UIColor.secondarySystemBackground))
        .cornerRadius(12)
    }

    private func stepCard(number: Int, text: String) -> some View {
        HStack(alignment: .top, spacing: 12) {
            Circle()
                .fill(Color.kgmOrange)
                .frame(width: 28, height: 28)
                .overlay(
                    Text("\(number)")
                        .font(.system(size: 14, weight: .bold))
                        .foregroundColor(.white)
                )

            Text(text)
                .font(.system(size: 13))
                .foregroundColor(.primary)
                .lineSpacing(2)

            Spacer()
        }
        .padding()
        .background(Color(UIColor.secondarySystemBackground))
        .cornerRadius(8)
    }
}

#Preview {
    ProductToolKit(product: Product(
        id: 1,
        name: "Test Ürün",
        slug: "test-urun",
        description: "Test açıklaması",
        brand: "Test Brand",
        barcode: "123456789",
        priceCents: 9999,
        price: "99,99 ₺",
        compareAtPriceCents: nil,
        stockQuantity: 10,
        imageUrl: nil,
        categories: nil
    ))
}
