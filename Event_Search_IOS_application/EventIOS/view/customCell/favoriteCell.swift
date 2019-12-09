//
//  favortieCell.swift
//  hw9
//
//  Created by apple on 11/27/18.
//  Copyright © 2018 apple. All rights reserved.
//

import UIKit
import SwipeCellKit

class favoriteCell: SwipeTableViewCell {
    
    @IBOutlet weak var title: UILabel!
    @IBOutlet weak var favoriteImage: UIImageView!
    @IBOutlet weak var venue: UILabel!
    @IBOutlet weak var date: UILabel!
    
    /**
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    **/

}
