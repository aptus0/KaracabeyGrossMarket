import Foundation
import UIKit

class ImageCacheManager {
    static let shared = ImageCacheManager()
    private let fileManager = FileManager.default
    private let cacheDirectory: URL
    private let imageCache = NSCache<NSString, UIImage>()

    init() {
        let paths = fileManager.urls(for: .cachesDirectory, in: .userDomainMask)
        cacheDirectory = paths[0].appendingPathComponent("ImageCache")

        try? fileManager.createDirectory(at: cacheDirectory, withIntermediateDirectories: true)
        imageCache.totalCostLimit = 100 * 1024 * 1024
    }

    func cacheImage(_ image: UIImage, for url: String) {
        let key = NSString(string: url.hashValue.description)
        imageCache.setObject(image, forKey: key, cost: Int(image.pngData()?.count ?? 0))

        if let data = image.jpegData(compressionQuality: 0.8) {
            let fileURL = cacheDirectory.appendingPathComponent(url.hashValue.description)
            try? data.write(to: fileURL)
        }
    }

    func cachedImage(for url: String) -> UIImage? {
        let key = NSString(string: url.hashValue.description)

        if let cached = imageCache.object(forKey: key) {
            return cached
        }

        let fileURL = cacheDirectory.appendingPathComponent(url.hashValue.description)
        if let data = try? Data(contentsOf: fileURL), let image = UIImage(data: data) {
            imageCache.setObject(image, forKey: key)
            return image
        }

        return nil
    }

    func clearCache() {
        imageCache.removeAllObjects()
        try? fileManager.removeItem(at: cacheDirectory)
        try? fileManager.createDirectory(at: cacheDirectory, withIntermediateDirectories: true)
    }

    func cacheSize() -> Int {
        guard let contents = try? fileManager.contentsOfDirectory(at: cacheDirectory, includingPropertiesForKeys: nil) else {
            return 0
        }
        var size = 0
        for file in contents {
            if let attrs = try? fileManager.attributesOfItem(atPath: file.path),
               let fileSize = attrs[.size] as? Int {
                size += fileSize
            }
        }
        return size
    }
}
