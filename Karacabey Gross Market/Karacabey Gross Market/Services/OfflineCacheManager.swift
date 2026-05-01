import Foundation

class OfflineCacheManager {
    static let shared = OfflineCacheManager()
    private let fileManager = FileManager.default
    private let cacheDirectory: URL

    init() {
        let paths = fileManager.urls(for: .cachesDirectory, in: .userDomainMask)
        cacheDirectory = paths[0].appendingPathComponent("OfflineCache")
        try? fileManager.createDirectory(at: cacheDirectory, withIntermediateDirectories: true)
    }

    func saveData<T: Encodable>(_ data: T, for key: String) {
        do {
            let encoder = JSONEncoder()
            let encoded = try encoder.encode(data)
            let fileURL = cacheDirectory.appendingPathComponent(key)
            try encoded.write(to: fileURL)
        } catch {
            print("Failed to cache data: \(error)")
        }
    }

    func getData<T: Decodable>(for key: String) -> T? {
        do {
            let fileURL = cacheDirectory.appendingPathComponent(key)
            let data = try Data(contentsOf: fileURL)
            let decoder = JSONDecoder()
            return try decoder.decode(T.self, from: data)
        } catch {
            return nil
        }
    }

    func removeData(for key: String) {
        let fileURL = cacheDirectory.appendingPathComponent(key)
        try? fileManager.removeItem(at: fileURL)
    }

    func clearCache() {
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
