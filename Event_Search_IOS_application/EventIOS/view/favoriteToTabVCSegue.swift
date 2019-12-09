//
//  favoriteToTabVCSegue.swift
//  hw9
//
//  Created by apple on 11/28/18.
//  Copyright © 2018 apple. All rights reserved.
//

import UIKit

class favoriteToTabVCSegue: UIStoryboardSegue {
   
        override func perform() {
            
            let src = self.source
            let dst = self.destination
            src.navigationController?.pushViewController(dst, animated: true)
        }

}
