import SwiftUI

struct OrderTrackingView: View {
    let order: Order
    @StateObject private var viewModel = OrderDetailViewModel()
    @State private var autoRefresh = true

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 20) {
                // Order Header
                VStack(alignment: .leading, spacing: 8) {
                    HStack {
                        VStack(alignment: .leading, spacing: 4) {
                            Text("Sipariş #\(order.number)")
                                .font(.poppins(weight: .bold, size: 18))
                                .foregroundColor(.primary)
                            Text(order.createdAt)
                                .font(.poppins(weight: .regular, size: 12))
                                .foregroundColor(.secondary)
                        }
                        Spacer()
                        VStack(alignment: .trailing, spacing: 4) {
                            Text(order.formattedTotal)
                                .font(.poppins(weight: .bold, size: 20))
                                .foregroundColor(.kgmOrange)
                            Text(order.statusLabel)
                                .font(.poppins(weight: .semibold, size: 11))
                                .foregroundColor(.white)
                                .padding(.horizontal, 12)
                                .padding(.vertical, 6)
                                .background(Color(order.statusColor))
                                .cornerRadius(8)
                        }
                    }
                }
                .padding()
                .background(Color(.secondarySystemBackground))
                .cornerRadius(12)

                // Payment Status
                if let payment = order.payment {
                    VStack(alignment: .leading, spacing: 12) {
                        Text("Ödeme Durumu")
                            .font(.poppins(weight: .bold, size: 16))
                            .foregroundColor(.primary)

                        HStack(spacing: 12) {
                            Circle()
                                .fill(paymentStatusColor(payment.status))
                                .frame(width: 40, height: 40)
                                .overlay(
                                    Image(systemName: paymentStatusIcon(payment.status))
                                        .font(.system(size: 16, weight: .bold))
                                        .foregroundColor(.white)
                                )

                            VStack(alignment: .leading, spacing: 4) {
                                Text(paymentStatusLabel(payment.status))
                                    .font(.poppins(weight: .semibold, size: 14))
                                    .foregroundColor(.primary)
                                Text(payment.displayAmount)
                                    .font(.poppins(weight: .regular, size: 12))
                                    .foregroundColor(.secondary)
                            }

                            Spacer()

                            if payment.status == .pending {
                                Button(action: { Task { await viewModel.refreshPaymentStatus() } }) {
                                    Image(systemName: "arrow.clockwise")
                                        .font(.system(size: 14, weight: .semibold))
                                        .foregroundColor(.kgmOrange)
                                }
                            }
                        }
                        .padding()
                        .background(Color(.secondarySystemBackground))
                        .cornerRadius(10)
                    }
                    .padding()
                    .background(Color(.systemBackground))
                    .cornerRadius(12)
                }

                // Order Items
                VStack(alignment: .leading, spacing: 12) {
                    Text("Ürünler")
                        .font(.poppins(weight: .bold, size: 16))
                        .foregroundColor(.primary)

                    if let items = order.items {
                        ForEach(items) { item in
                            HStack(spacing: 12) {
                                VStack(alignment: .leading, spacing: 4) {
                                    Text(item.productName)
                                        .font(.poppins(weight: .semibold, size: 14))
                                        .foregroundColor(.primary)
                                        .lineLimit(2)

                                    HStack(spacing: 8) {
                                        Text("Adet: \(item.quantity)")
                                            .font(.poppins(weight: .regular, size: 12))
                                            .foregroundColor(.secondary)
                                        Spacer()
                                        Text(item.formattedPrice)
                                            .font(.poppins(weight: .bold, size: 12))
                                            .foregroundColor(.kgmOrange)
                                    }
                                }
                                Spacer()
                            }
                            .padding()
                            .background(Color(.secondarySystemBackground))
                            .cornerRadius(10)
                        }
                    }
                }
                .padding()
                .background(Color(.systemBackground))
                .cornerRadius(12)

                // Shipping Info
                VStack(alignment: .leading, spacing: 12) {
                    Text("Teslimat Bilgileri")
                        .font(.poppins(weight: .bold, size: 16))
                        .foregroundColor(.primary)

                    VStack(alignment: .leading, spacing: 8) {
                        InfoRow(label: "İsim", value: order.number)
                        Divider()
                        InfoRow(label: "Telefon", value: order.number)
                        Divider()
                        if let city = order.address?.city {
                            InfoRow(label: "Şehir", value: city)
                            Divider()
                        }
                        if let address = order.address?.addressLine {
                            InfoRow(label: "Adres", value: address)
                        }
                    }
                    .padding()
                    .background(Color(.secondarySystemBackground))
                    .cornerRadius(10)
                }
                .padding()
                .background(Color(.systemBackground))
                .cornerRadius(12)

                Spacer().frame(height: 20)
            }
            .padding()
        }
        .navigationTitle("Sipariş Takibi")
        .navigationBarTitleDisplayMode(.inline)
        .task {
            await viewModel.load(orderId: order.id)
        }
    }

    private func paymentStatusColor(_ status: PaymentStatus) -> Color {
        switch status {
        case .completed: return .green
        case .processing: return .blue
        case .pending: return .orange
        case .failed, .refunded: return .red
        }
    }

    private func paymentStatusIcon(_ status: PaymentStatus) -> String {
        switch status {
        case .completed: return "checkmark"
        case .processing: return "hourglass"
        case .pending: return "clock"
        case .failed, .refunded: return "xmark"
        }
    }

    private func paymentStatusLabel(_ status: PaymentStatus) -> String {
        switch status {
        case .completed: return "Ödeme Tamamlandı"
        case .processing: return "Ödeme İşleniyor"
        case .pending: return "Ödeme Bekleniyor"
        case .failed: return "Ödeme Başarısız"
        case .refunded: return "Para İade Edildi"
        }
    }
}

private struct InfoRow: View {
    let label: String
    let value: String

    var body: some View {
        HStack {
            Text(label)
                .font(.poppins(weight: .semibold, size: 12))
                .foregroundColor(.secondary)
            Spacer()
            Text(value)
                .font(.poppins(weight: .regular, size: 12))
                .foregroundColor(.primary)
        }
    }
}
