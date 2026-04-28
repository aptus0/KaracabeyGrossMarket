import SwiftUI

extension Color {
    static let kgmOrange = Color(hex: "#FF7A00")
    static let kgmDarkGray = Color(hex: "#2B2F36")
    static let kgmGray = Color(hex: "#6B7177")
    static let kgmWhite = Color(hex: "#FFFFFF")
    
    init(hex: String) {
        let hex = hex.trimmingCharacters(in: CharacterSet.alphanumerics.inverted)
        var int: UInt64 = 0
        Scanner(string: hex).scanHexInt64(&int)
        let a, r, g, b: UInt64
        switch hex.count {
        case 3: // RGB (12-bit)
            (a, r, g, b) = (255, (int >> 8) * 17, (int >> 4 & 0xF) * 17, (int & 0xF) * 17)
        case 6: // RGB (24-bit)
            (a, r, g, b) = (255, int >> 16, int >> 8 & 0xFF, int & 0xFF)
        case 8: // ARGB (32-bit)
            (a, r, g, b) = (int >> 24, int >> 16 & 0xFF, int >> 8 & 0xFF, int & 0xFF)
        default:
            (a, r, g, b) = (1, 1, 1, 0)
        }

        self.init(
            .sRGB,
            red: Double(r) / 255,
            green: Double(g) / 255,
            blue:  Double(b) / 255,
            opacity: Double(a) / 255
        )
    }
}

extension Font {
    static func poppins(weight: Font.Weight = .regular, size: CGFloat) -> Font {
        let fontName: String
        switch weight {
        case .light: fontName = "Poppins-Light"
        case .medium: fontName = "Poppins-Medium"
        case .semibold: fontName = "Poppins-SemiBold"
        case .bold: fontName = "Poppins-Bold"
        default: fontName = "Poppins-Regular"
        }
        return .custom(fontName, size: size)
    }
}
