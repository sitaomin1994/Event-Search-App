//
//  RadioButton.swift
//  hw9
//
//  Created by apple on 11/21/18.
//  Copyright © 2018 apple. All rights reserved.
//

import Foundation
import UIKit

class RadioButton:UIButton{
    
    var options:Array<RadioButton>?
    
    override func awakeFromNib() {
        self.layer.cornerRadius = 5
        self.layer.borderWidth = 2.0
        self.layer.masksToBounds = true
    }
    
    func unselectAlternateButtons(){
        if options != nil {
            self.isSelected = true
            
            for aButton:RadioButton in options! {
                aButton.isSelected = false
            }
        }else{
            toggleButton()
        }
    }
    
    override func touchesBegan(_ touches: Set<UITouch>, with event: UIEvent?) {
        unselectAlternateButtons()
        super.touchesBegan(touches, with: event)
    }
    
    func toggleButton(){
        self.isSelected = !isSelected
    }
    
    override var isSelected: Bool {
        didSet {
            if isSelected {
                self.layer.borderColor = UIColor.turquoise.cgColor
            } else {
                self.layer.borderColor = UIColor.grey_99.cgColor
            }
        }
    }
    
    
}
