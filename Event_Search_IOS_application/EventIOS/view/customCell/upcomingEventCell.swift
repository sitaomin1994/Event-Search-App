//
//  upcomingEventCellTableViewCell.swift
//  hw9
//
//  Created by apple on 11/26/18.
//  Copyright © 2018 apple. All rights reserved.
//

import UIKit

class upcomingEventCell: UITableViewCell {

    //MARK: Property
    
    @IBOutlet weak var stack: UIStackView!
    
    @IBOutlet weak var title: UILabel!
    @IBOutlet weak var artist: UILabel!
    @IBOutlet weak var time: UILabel!
    @IBOutlet weak var type: UILabel!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
