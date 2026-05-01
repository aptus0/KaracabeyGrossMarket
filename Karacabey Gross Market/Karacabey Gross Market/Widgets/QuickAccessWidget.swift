import SwiftUI
import WidgetKit

// MARK: - Quick Access Widget

struct QuickAccessWidget: Widget {
    let kind: String = "QuickAccessWidget"

    var body: some WidgetConfiguration {
        StaticConfiguration(kind: kind, provider: QuickAccessProvider()) { entry in
            QuickAccessWidgetView(entry: entry)
        }
        .configurationDisplayName("Hızlı Erişim")
        .description("Sık kullanılan sayfalarıza hızlı erişim")
        .supportedFamilies([.systemSmall, .systemMedium])
    }
}

// MARK: - Timeline Entry
struct QuickAccessEntry: TimelineEntry {
    let date: Date
    let cartCount: Int = 0
    let favoriteCount: Int = 0
}

// MARK: - Timeline Provider
struct QuickAccessProvider: TimelineProvider {
    func placeholder(in context: Context) -> QuickAccessEntry {
        QuickAccessEntry(date: Date())
    }

    func getSnapshot(in context: Context, completion: @escaping (QuickAccessEntry) -> Void) {
        let entry = QuickAccessEntry(date: Date())
        completion(entry)
    }

    func getTimeline(in context: Context, completion: @escaping (Timeline<QuickAccessEntry>) -> Void) {
        let entry = QuickAccessEntry(date: Date())
        // Refresh every 1 hour
        let nextUpdate = Calendar.current.date(byAdding: .hour, value: 1, to: Date())!
        let timeline = Timeline(entries: [entry], policy: .after(nextUpdate))
        completion(timeline)
    }
}

// MARK: - Widget View
struct QuickAccessWidgetView: View {
    var entry: QuickAccessEntry

    var body: some View {
        ZStack {
            LinearGradient(
                gradient: Gradient(colors: [Color.kgmOrange.opacity(0.1), Color.clear]),
                startPoint: .topLeading,
                endPoint: .bottomTrailing
            )

            VStack(alignment: .leading, spacing: 12) {
                Text("⚡ Hızlı Erişim")
                    .font(.poppins(weight: .bold, size: 14))
                    .foregroundColor(.primary)

                VStack(spacing: 8) {
                    QuickAccessButton(
                        icon: "cart.fill",
                        label: "Sepet",
                        count: entry.cartCount,
                        urlString: "karacabey://cart"
                    )

                    QuickAccessButton(
                        icon: "heart.fill",
                        label: "Favoriler",
                        count: entry.favoriteCount,
                        urlString: "karacabey://favorites"
                    )

                    QuickAccessButton(
                        icon: "magnifyingglass",
                        label: "Ara",
                        count: nil,
                        urlString: "karacabey://search"
                    )

                    QuickAccessButton(
                        icon: "person.fill",
                        label: "Profilim",
                        count: nil,
                        urlString: "karacabey://profile"
                    )
                }
            }
            .padding(12)
        }
    }
}

// MARK: - Quick Access Button Component
struct QuickAccessButton: View {
    let icon: String
    let label: String
    let count: Int?
    let urlString: String

    var body: some View {
        Link(destination: URL(string: urlString) ?? URL(string: "karacabey://")!) {
            HStack(spacing: 10) {
                Image(systemName: icon)
                    .font(.system(size: 16, weight: .semibold))
                    .foregroundColor(.kgmOrange)
                    .frame(width: 24)

                Text(label)
                    .font(.poppins(weight: .medium, size: 12))
                    .foregroundColor(.primary)

                Spacer()

                if let count = count, count > 0 {
                    Text("\(count)")
                        .font(.poppins(weight: .bold, size: 11))
                        .foregroundColor(.white)
                        .padding(.horizontal, 6)
                        .padding(.vertical, 2)
                        .background(Color.kgmOrange)
                        .cornerRadius(4)
                }
            }
            .padding(.horizontal, 10)
            .padding(.vertical, 8)
            .background(Color(.secondarySystemBackground))
            .cornerRadius(8)
        }
    }
}

// MARK: - Preview
#Preview(as: .systemSmall) {
    QuickAccessWidget()
} timeline: {
    QuickAccessEntry(date: .now)
}
