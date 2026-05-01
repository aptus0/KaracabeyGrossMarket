import SwiftUI
import WidgetKit

// MARK: - Order Status Widget

struct OrderStatusWidget: Widget {
    let kind: String = "OrderStatusWidget"

    var body: some WidgetConfiguration {
        StaticConfiguration(kind: kind, provider: OrderStatusProvider()) { entry in
            OrderStatusWidgetView(entry: entry)
        }
        .configurationDisplayName("Son Siparişim")
        .description("En son siparişinizin durumunu gösterir")
        .supportedFamilies([.systemSmall, .systemMedium])
    }
}

// MARK: - Time Entry
struct OrderStatusEntry: TimelineEntry {
    let date: Date
    let order: Order?
    let errorMessage: String?
}

// MARK: - Timeline Provider
struct OrderStatusProvider: TimelineProvider {
    func placeholder(in context: Context) -> OrderStatusEntry {
        OrderStatusEntry(
            date: Date(),
            order: nil,
            errorMessage: nil
        )
    }

    func getSnapshot(in context: Context, completion: @escaping (OrderStatusEntry) -> Void) {
        let entry = OrderStatusEntry(
            date: Date(),
            order: nil,
            errorMessage: nil
        )
        completion(entry)
    }

    func getTimeline(in context: Context, completion: @escaping (Timeline<OrderStatusEntry>) -> Void) {
        Task {
            do {
                // Fetch latest order
                let response: ArrayResponse<Order> = try await APIClient.shared.request(OrdersEndpoint.list)
                let latestOrder = response.data.first

                let entry = OrderStatusEntry(
                    date: Date(),
                    order: latestOrder,
                    errorMessage: nil
                )

                // Refresh every 30 minutes
                let nextUpdate = Calendar.current.date(byAdding: .minute, value: 30, to: Date())!
                let timeline = Timeline(entries: [entry], policy: .after(nextUpdate))
                completion(timeline)
            } catch {
                let entry = OrderStatusEntry(
                    date: Date(),
                    order: nil,
                    errorMessage: "Yüklenemedi"
                )
                let timeline = Timeline(entries: [entry], policy: .after(Calendar.current.date(byAdding: .minute, value: 5, to: Date())!))
                completion(timeline)
            }
        }
    }
}

// MARK: - Widget View
struct OrderStatusWidgetView: View {
    var entry: OrderStatusEntry

    var body: some View {
        ZStack {
            Color(.systemBackground)

            VStack(alignment: .leading, spacing: 12) {
                HStack {
                    Text("📦 Son Siparişim")
                        .font(.poppins(weight: .bold, size: 14))
                        .foregroundColor(.primary)

                    Spacer()

                    if let order = entry.order {
                        Text(order.statusLabel)
                            .font(.poppins(weight: .semibold, size: 10))
                            .foregroundColor(.white)
                            .padding(.horizontal, 8)
                            .padding(.vertical, 4)
                            .background(Color(order.statusColor))
                            .cornerRadius(6)
                    }
                }

                if let order = entry.order {
                    VStack(alignment: .leading, spacing: 6) {
                        Text("#\(order.number)")
                            .font(.poppins(weight: .semibold, size: 12))
                            .foregroundColor(.secondary)

                        HStack {
                            Text(order.formattedTotal)
                                .font(.poppins(weight: .bold, size: 16))
                                .foregroundColor(.kgmOrange)

                            Spacer()

                            VStack(alignment: .trailing, spacing: 2) {
                                Text("Sipariş Tarihi")
                                    .font(.caption)
                                    .foregroundColor(.secondary)

                                Text(order.createdAt.prefix(10))
                                    .font(.poppins(weight: .semibold, size: 10))
                                    .foregroundColor(.primary)
                            }
                        }
                    }
                } else if let error = entry.errorMessage {
                    Text(error)
                        .font(.caption)
                        .foregroundColor(.secondary)
                        .frame(maxWidth: .infinity, alignment: .center)
                } else {
                    ProgressView()
                        .frame(maxWidth: .infinity, alignment: .center)
                }
            }
            .padding(12)
        }
        .widgetURL(URL(string: "karacabey://orders")!)
    }
}

// MARK: - Preview
#Preview(as: .systemSmall) {
    OrderStatusWidget()
} timeline: {
    OrderStatusEntry(date: .now, order: nil, errorMessage: nil)
}
