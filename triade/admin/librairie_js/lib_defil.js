/************************************************************

Last updated: 28.06.2002    par Taesch  Eric
*************************************************************/

<!--
var ns4 = (document.layers)? true:false;                           //NS 4
var ie4 = (document.all)? true:false;                                   //IE 4
var dom = (document.getElementById)? true:false;   //DOM

function GetNS4Object(MyID,MyDocument)
        {
        var MyObject= eval('MyDocument.'+MyID);
  if (!(MyObject))
          for(var i=0;i<MyDocument.layers.length;i++)
                        {
                        MyObject = GetNS4Object(MyID,MyDocument.layers[i].document);
                        if (MyObject) break;
                        }
  return MyObject;
  }


function GetObject(MyID)
        {
  if (dom) return document.getElementById(MyID);
  if (ie4) return document.all[MyID];
  if (ns4) return GetNS4Object(MyID,window.document);
  return 0;
        }

function GetCSS(MyObject)
        {
  if (dom || ie4) return MyObject.style;
  else if (ns4) return MyObject;
  else return 0;
        }

function GetHeight(MyObject)
        {
  if (dom || ie4) return MyObject.offsetHeight;
  else if (ns4) return MyObject.clip.height;
  else return 0;
        }

function GetWidth(MyObject)
        {
  if (dom || ie4) return MyObject.offsetWidth;
  else if (ns4) return MyObject.clip.width;
  else return 0;
        }

function GetTop(MyObject)
        {
  if (dom || ie4)
          return (MyObject.offsetTop);
  if (ns4) return MyObject.y;
          return 0;
        }

function GetLeft(MyObject)
        {
  if (dom || ie4) return MyObject.offsetLeft;
  if (ns4) return MyObject.x;
  return 0;
        }

function MoveObject(myX,myY)
        {
  this.X = myX;
  this.Y = myY;
  this.CSS.left=this.X;
  this.CSS.top=this.Y;
  }

function MoveObjectUp(mystep)
        {
  this.Y -= mystep;
  this.CSS.top=this.Y;
  }

function MoveObjectDown(mystep)
        {
  this.Y += mystep;
  this.CSS.top=this.Y;
  }

function CreateObject(DivId,MyObject)
                {
    if (MyObject)
            this.Object = MyObject;
    else
            this.Object = GetObject(DivId);

    if (this.Object)
            {
      this.CSS = GetCSS(this.Object);
      this.Height = GetHeight(this.Object);
      this.Width = GetWidth(this.Object);
      this.X = GetTop(this.Object);
      this.Y = GetLeft(this.Object);
      this.Move = MoveObject;
      this.Up = MoveObjectUp;
      this.Down = MoveObjectDown;
      }
    return this;
    }

function DelTextNode(MyObject)
        {
  var node = MyObject.firstChild;
  var next;

  while (node)
          {
    next = node.nextSibling;
    if (node.nodeType == 3)
      MyObject.removeChild(node);
    node = next;
          }
  }

function CreateChildren(MyObject,HoriSpacer,VertSpacer)
        {
  var i=0;
  var MyChildren = new Array();
  if (dom)
          {
    DelTextNode(MyObject);
    for (i=0;i<MyObject.childNodes.length;i++)
            {
            MyChildren[i] = new CreateObject(0,MyObject.childNodes[i]);
      MyChildren[i].Move(i*HoriSpacer,i*VertSpacer);
      }
    return MyChildren;
    }
  if (ie4)
          {
    for (i=0;i<MyObject.children.length;i++)
            {
      MyChildren[i] = new CreateObject(0,MyObject.children(i));
      MyChildren[i].Move(i*HoriSpacer,i*VertSpacer);
      }
    return MyChildren;
    }
  if (ns4)
          {
    for (i=0;i<MyObject.document.layers.length;i++)
            {
      MyChildren[i] = new CreateObject(0,MyObject.layers[i]);
      MyChildren[i].Move(i*HoriSpacer,i*VertSpacer);
      }
    return MyChildren;
    }
  }

function ScrollUp()
        {
  var MyInterval = this.Interval1;
  this.stop();
  if (this.Children[this.FirstChildren].Y<-this.threshold)
          {
    MyInterval = this.Interval2;
           this.Children[this.FirstChildren].Down(this.TotalHeight);
    if (this.FirstChildren<this.Children.length-1)
            {
            this.FirstChildren++;
      this.threshold += this.Children[this.FirstChildren].Height;
      }
    else
            {
            this.FirstChildren = 0;
      this.threshold = this.Children[this.FirstChildren].Height+this.Spacer;
      }
    }
        for (var i=0;i<this.Children.length;i++)
          {
    this.Children[i].Up(this.Step);
    }
  this.ProcessId = setTimeout(this.name + '.start()', MyInterval);
  }


function ScrollStop()
        {
  if (this.ProcessId)
    clearTimeout(this.ProcessId);
  this.ProcessId = null;
        }

function Box(BoxName, DivId, myStep, myTempo1, myTempo2, mySpacer)
        {
  this.name     = BoxName;
  this.Step  = myStep ? myStep : 1;
  this.Interval1 = myTempo1 ? myTempo1 : 100;
  this.Interval2 = myTempo2 ? myTempo2 : 100;
  this.Spacer = mySpacer ? mySpacer : 0;
  this.ProcessId        = null;
  this.Container = new CreateObject(DivId);
  this.Children = new CreateChildren(this.Container.Object,0,mySpacer);
  this.FirstChildren = 0;
  this.LastChildren = this.Children.length-1;
  this.threshold = this.Children[0].Height+this.Spacer;
  var myHeight = 0;
  for (var i=0;i<this.Children.length;i++)
          {
    myHeight += (this.Children[i].Height + this.Spacer);
    }
  this.TotalHeight = myHeight;
  this.Container.visibility = 'visible';
        }

Box.prototype.start = ScrollUp;
Box.prototype.stop = ScrollStop;


//------//
var MyBox = null;

function Init()
        {
        MyBox = new Box('MyBox', 'conteneur', 1, 50, 200, 20);
  MyBox.start();
  }

