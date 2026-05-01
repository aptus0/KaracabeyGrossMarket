import SwiftUI
import WidgetKit

// MARK: - Cargo Tracking Widget

struct CargoTrackingWidget: Widget {
    let kind: String = "CargoTrackingWidget"

    var body: some WidgetConfiguration {
        StaticConfiguration(kind: kind, provider: CargoTrackingProvider()) { entry in
            CargoTrackingWidgetView(entry: entry)
        }
        .configurationDisplayName("Kargo Takibi")
        .description("Aktif siparişinizin kargo durumunu takip edin")
        .supportedFamilies([.systemSmall, .systemMedium, .systemLarge])
    }
}

// MARK: - Timeline Entry
struct CargoEntry: TimelineEntry {
    let date: Date
    let orders: [Order]
    let errorMessage: String?
}

// MARK: - Timeline Provider
struct CargoTrackingProvider: TimelineProvider {
    func placeholder(in context: Context) -> CargoEntry {
        CargoEntry(date: Date(), orders: [], errorMessage: nil)
    }

    func getSnapshot(in context: Context, completion: @escaping (CargoEntry) -> Void) {
        let entry = CargoEntry(date: Date(), orders: [], errorMessage: nil)
        completion(entry)
    }

    func getTimeline(in context: Context, completion: @escaping (Timeline<CargoEntry>) -> Void) {
        Task {
            do {
                let response: ArrayResponse<Order> = try await APIClient.shared.request(OrdersEndpoint.list)
                // Filter shipped/processing orders
                let activeOrders = response.data.filter { ["shipped", "processing"].contains($0.status) }

                let entry = CargoEntry(
                    date: Date(),
                    orders: activeOrders,
                    errorMessage: nil
                )

                // Refresh every 15 minutes
                let nextUpdate = Calendar.current.date(byAdding: .minute, value: 15, to: Date())!
                let timeline = Timeline(entries: [entry], policy: .after(nextUpdate))
                completion(timeline)
            } catch {
                let entry = CargoEntry(
                    date: Date(),
                    orders: [],
                    errorMessage: "Yüklenemedi"
                )
                let timeline = Timeline(entries: [entry], policy: .after(Calendar.current.date(byAdding: .minute, value: 5, to: Date())!))
                completion(timeline)
            }
        }
    }
}

// MARK: - Widget View
struct CargoTrackingWidgetView: View {
    var entry: CargoEntry

    var body: some View {
        ZStack {
            Color(.systemBackground)

            VStack(alignment: .leading, spacing: 12) {
                HStack {
                    Text("🚚 Kargo Takibi")
                        .font(.poppins(weight: .bold, size: 14))
                        .foregroundColor(.primary)

                    Spacer()

                    Text("\(entry.orders.count) Aktif")
                        .font(.caption)
                        .foregroundColor(.secondary)
                }

                if !entry.orders.isEmpty {
                    VStack(spacing: 8) {
                        ForEach(entry.orders.prefix(3)) { order in
                            HStack(spacing: 8) {
                                VStack(alignment: .leading, spacing: 3) {
                                    Text("#\(order.number)")
                                        .font(.poppins(weight: .semibold, size: 11))
                                        .foregroundColor(.primary)

                                    Text(order.statusLabel)
                                        .font(.caption)
                                        .foregroundColor(.secondary)
                                }

                                Spacer()

                                HStack(spacing: 4) {
                                    Image(systemName: statusIcon(order.status))
                                        .font(.system(size: 12, weight: .semibold))
                                        .foregroundColor(.kgmOrange)
                                }
                            }
                            .padding(8)
                            .background(Color(.secondarySystemBackground))
                            .cornerRadius(8)
                        }
                    }
                } else if let error = entry.errorMessage {
                    Text(error)
                        .font(.caption)
                        .foregroundColor(.secondary)
                        .frame(maxWidth: .infinity, alignment: .center)
                } else {
                    Text("Aktif kargo yok")
                        .font(.caption)
                        .foregroundColor(.secondary)
                        .frame(maxWidth: .infinity, alignment: .center)
                }
            }
            .padding(12)
        }
        .widgetURL(URL(string: "karacabey://orders")!)
    }

    private func statusIcon(_ status: String) -> String {
        switch status {
        case "shipped": return "truck.fill"
        case "processing": return "hourglass"
        case "delivered": return "checkmark.circle.fill"
        default: return "package.fill"
        }
    }
}

// MARK: - Preview
#Preview(as: .systemMedium) {
    CargoTrackingWidget()
} timeline: {
    CargoEntry(date: .now, orders: [], errorMessage: nil)
}
