//  event.swift
//  hw9
//
//  Created by apple on 11/22/18.
//  Copyright © 2018 apple. All rights reserved.
//

import Foundation

struct Event:Codable{
    var name: String
    var id: String
    var url: String
    var venueName: String
    var date: String
    var category:String
    
    init(name: String, id: String, url:String, venueName:String, date: String, category: String) {
        self.name = name
        self.id = id
        self.url = url
        self.venueName = venueName
        if date == " "{
            self.date = "N/A"
        }
        else{
            self.date = date
        }
        self.category = category
    }
}
