import SwiftUI

struct OrdersView: View {
    @StateObject private var viewModel = OrdersViewModel()

    var body: some View {
        Group {
            if viewModel.isLoading {
                ProgressView("Siparişler yükleniyor…").frame(maxWidth: .infinity, maxHeight: .infinity)
            } else if let err = viewModel.errorMessage {
                VStack(spacing: 12) {
                    Image(systemName: "exclamationmark.triangle").font(.largeTitle).foregroundColor(.orange)
                    Text(err).foregroundColor(.secondary)
                    Button("Tekrar Dene") { Task { await viewModel.load() } }
                }
                .frame(maxWidth: .infinity, maxHeight: .infinity)
            } else if viewModel.orders.isEmpty {
                VStack(spacing: 16) {
                    Image(systemName: "shippingbox").font(.system(size: 64)).foregroundColor(.gray.opacity(0.4))
                    Text("Henüz siparişiniz yok").font(.poppins(weight: .medium, size: 16)).foregroundColor(.secondary)
                }
                .frame(maxWidth: .infinity, maxHeight: .infinity)
            } else {
                List(viewModel.orders) { order in
                    NavigationLink(destination: OrderTrackingView(order: order)) {
                        OrderRow(order: order)
                    }
                }
                .listStyle(.insetGrouped)
            }
        }
        .navigationTitle("Siparişlerim")
        .task { await viewModel.load() }
    }
}

struct OrderRow: View {
    let order: Order
    var body: some View {
        VStack(alignment: .leading, spacing: 6) {
            HStack {
                Text("#\(order.number)").font(.poppins(weight: .bold, size: 15))
                Spacer()
                StatusBadge(label: order.statusLabel, colorName: order.statusColor)
            }
            Text(order.formattedTotal).font(.poppins(weight: .medium, size: 14)).foregroundColor(.kgmOrange)
            Text(order.createdAt.prefix(10)).font(.caption).foregroundColor(.kgmGray)
        }
        .padding(.vertical, 4)
    }
}

struct OrderDetailView: View {
    let order: Order

    var body: some View {
        List {
            Section("Durum") {
                StatusBadge(label: order.statusLabel, colorName: order.statusColor)
                    .padding(.vertical, 4)
            }
            if let items = order.items {
                Section("Ürünler") {
                    ForEach(items) { item in
                        HStack {
                            Text(item.productName).font(.poppins(weight: .medium, size: 14))
                            Spacer()
                            Text("\(item.quantity) x \(item.formattedPrice)")
                                .font(.poppins(weight: .regular, size: 13)).foregroundColor(.kgmGray)
                        }
                    }
                }
            }
            Section("Toplam") {
                HStack {
                    Text("Sipariş Toplamı").font(.poppins(weight: .bold, size: 15))
                    Spacer()
                    Text(order.formattedTotal).font(.poppins(weight: .bold, size: 15)).foregroundColor(.kgmOrange)
                }
            }
        }
        .listStyle(.insetGrouped)
        .navigationTitle("Sipariş #\(order.number)")
    }
}

struct StatusBadge: View {
    let label: String
    let colorName: String

    private var color: Color {
        switch colorName {
        case "green": return .green
        case "red":   return .red
        case "blue":  return .blue
        default:      return .orange
        }
    }

    var body: some View {
        Text(label)
            .font(.poppins(weight: .medium, size: 12))
            .padding(.horizontal, 10).padding(.vertical, 4)
            .background(color.opacity(0.15))
            .foregroundColor(color)
            .cornerRadius(8)
    }
}
