//
//  Karacabey_Gross_MarketApp.swift
//  Karacabey Gross Market
//
//  Created by Samet on 24.04.2026.
//

import SwiftUI
import CoreData

@main
struct Karacabey_Gross_MarketApp: App {
    let persistenceController = PersistenceController.shared

    var body: some Scene {
        WindowGroup {
            RootView()
                .environment(\.managedObjectContext, persistenceController.container.viewContext)
        }
    }
}
