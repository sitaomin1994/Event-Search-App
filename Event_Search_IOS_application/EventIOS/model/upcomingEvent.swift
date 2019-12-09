//
//  upcomingEvent.swift
//  hw9
//
//  Created by apple on 11/26/18.
//  Copyright © 2018 apple. All rights reserved.
//

import Foundation

struct upcomingEvent{
    
    var title:String!
    var artist:String!
    var dateCompare:Date!
    var dateValue:Date!
    var noDate:Bool!
    var noTime:Bool!
    var type:String!
    var url:String!
    
    init(title:String,artist:String,dateCompare:Date,dateValue:Date,noDate:Bool,noTime:Bool, type:String, url:String){
        self.title = title
        self.artist = artist
        self.dateCompare = dateCompare
        self.dateValue = dateValue
        self.noDate = noDate
        self.noTime = noTime
        self.type = type
        self.url = url
    }
}
